<?php
// app/Http/Controllers/AiSearchController.php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Schema; // TAMBAHKAN INI DI ATAS
use Illuminate\Http\Request;
use App\Models\Document; // Ganti ke model Document yang sudah diperbaiki
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AiSearchController extends Controller
{
    /**
     * Handle AI search request
     */
    public function search(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'query' => 'required|string|min:3|max:255'
        ]);
        
        $query = $validated['query'];
        $startTime = microtime(true);
        
        Log::info('AI Search initiated', ['query' => $query, 'ip' => $request->ip()]);
        
        try {
            // 1. Cari dokumen berdasarkan query
            $documents = $this->searchDocuments($query);
            
            // 2. Dapatkan penjelasan AI
            $explanation = $this->getAiExplanation($query, $documents);
            
            // 3. Log performance
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            Log::info('AI Search completed', [
                'query' => $query,
                'results' => count($documents),
                'time_ms' => $executionTime,
                'provider' => $this->getActiveProvider()
            ]);
            
            return response()->json([
                'query' => $query,
                'explanation' => $explanation,
                'documents' => $documents,
                'total' => count($documents),
                'execution_time' => $executionTime,
                'provider' => $this->getActiveProvider(),
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Search Error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'query' => $query,
                'explanation' => 'Maaf, terjadi kesalahan dalam sistem pencarian. Silakan coba lagi.',
                'documents' => [],
                'total' => 0,
                'status' => 'error',
                'message' => 'System error occurred'
            ], 500);
        }
    }
    
    /**
     * Search documents with improved algorithm
     */
    private function searchDocuments($query)
    {
        try {
            $searchMethod = 'LIKE';
            
            // Gunakan model Document yang sudah diupdate
            if ($this->hasFulltextIndex()) {
                // FULLTEXT search dengan match against
                $documents = Document::query()
                    ->whereRaw("MATCH(judul, deskripsi, ringkasan) AGAINST(? IN NATURAL LANGUAGE MODE)", [$query])
                    ->with(['kategori', 'tags'])
                    ->published()
                    ->get();
                $searchMethod = 'FULLTEXT';
            } else {
                // Fallback ke LIKE search dengan query yang lebih optimal
                $documents = Document::query()
                    ->where(function ($q) use ($query) {
                        $q->where('judul', 'like', "%{$query}%")
                          ->orWhere('deskripsi', 'like', "%{$query}%")
                          ->orWhere('ringkasan', 'like', "%{$query}%");
                    })
                    ->with(['kategori', 'tags'])
                    ->published()
                    ->get();
            }
            
            // Hitung akurasi dan format hasil
            $documents = $documents->map(function ($doc) use ($query) {
                $accuracy = $this->calculateDocumentAccuracy($doc, $query);
                
                return [
                    'id' => $doc->id,
                    'title' => $doc->judul,
                    'slug' => $doc->slug,
                    'type' => $doc->jenis_dokumen_display ?? 'Dokumen',
                    'type_raw' => $doc->jenis_dokumen,
                    'year' => $doc->tahun_display,
                    'accuracy' => $accuracy,
                    'description' => $doc->short_description,
                    'url' => $doc->url,
                    'download_url' => $doc->download_url,
                    'category' => $doc->kategori_nama,
                    'category_id' => $doc->kategori_id,
                    'file_size' => $doc->formatted_file_size,
                    'icon' => $doc->getTypeIcon(),
                    'color' => $doc->getTypeColor(),
                    'created_at' => $doc->created_at ? $doc->created_at->format('d M Y') : null,
                    'views' => $doc->views,
                    'downloads' => $doc->downloads,
                ];
            })
            ->sortByDesc('accuracy')
            ->values()
            ->take(config('services.ai_search.max_results', 10));
            
            Log::debug('Document search completed', [
                'method' => $searchMethod,
                'query' => $query,
                'found' => $documents->count()
            ]);
            
            return $documents;
            
        } catch (\Exception $e) {
            Log::error('Search Documents Error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }
    
    /**
     * Calculate document accuracy with improved algorithm
     */
    private function calculateDocumentAccuracy($document, $query)
    {
        try {
            $queryLower = strtolower(trim($query));
            $titleLower = strtolower(trim($document->judul));
            
            // Base similarity scores
            similar_text($queryLower, $titleLower, $titleScore);
            
            // Description similarity
            $descScore = 0;
            if (!empty($document->deskripsi)) {
                $descLower = strtolower(substr($document->deskripsi, 0, 200));
                similar_text($queryLower, $descLower, $descScore);
            }
            
            // Ringkasan similarity
            $ringkasanScore = 0;
            if (!empty($document->ringkasan)) {
                $ringkasanLower = strtolower(substr($document->ringkasan, 0, 150));
                similar_text($queryLower, $ringkasanLower, $ringkasanScore);
            }
            
            // Baca weights dari config
            $weights = config('ai-search.documents.relevance_weights', [
                'judul' => 0.6,
                'deskripsi' => 0.3,
                'ringkasan' => 0.1
            ]);
            
            // Weighted average
            $accuracy = ($titleScore * $weights['judul']) + 
                       ($descScore * $weights['deskripsi']) + 
                       ($ringkasanScore * $weights['ringkasan']);
            
            // Bonus untuk exact match di judul
            if (strpos($titleLower, $queryLower) !== false) {
                $accuracy += 25; // Bonus besar untuk exact match
            }
            
            // Bonus untuk partial match di judul
            $words = explode(' ', $queryLower);
            $matchedWords = 0;
            foreach ($words as $word) {
                if (strlen($word) > 2 && strpos($titleLower, $word) !== false) {
                    $matchedWords++;
                }
            }
            if ($matchedWords > 0) {
                $accuracy += ($matchedWords / count($words)) * 15;
            }
            
            // Normalize antara 10-100%
            $accuracy = max(10, min(100, round($accuracy)));
            
            return $accuracy;
            
        } catch (\Exception $e) {
            Log::warning('Accuracy calculation failed', ['error' => $e->getMessage()]);
            return 50; // Default moderate accuracy
        }
    }
    
    /**
     * Get AI explanation based on configured provider
     */
    private function getAiExplanation($query, $documents)
    {
        // Check if AI search is enabled
        if (!config('services.ai_search.enabled', true)) {
            return $this->getFallbackExplanation($query, $documents);
        }
        
        $provider = config('services.ai_search.provider', 'fallback');
        $count = count($documents);
        
        Log::debug('Getting AI explanation', [
            'provider' => $provider,
            'query' => $query,
            'documents_found' => $count
        ]);
        
        try {
            switch ($provider) {
                case 'google_gemini':
                    return $this->getGeminiExplanation($query, $documents);
                    
                case 'openai':
                    return $this->getOpenAIExplanation($query, $documents);
                    
                case 'azure':
                    return $this->getAzureExplanation($query, $documents);
                    
                case 'fallback':
                default:
                    return $this->getFallbackExplanation($query, $documents);
            }
        } catch (\Exception $e) {
            Log::error('AI Explanation Error', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            
            // Fallback jika semua gagal
            if (config('services.ai_search.fallback.enabled', true)) {
                return $this->getFallbackExplanation($query, $documents);
            }
            
            return "Sistem penjelasan AI sedang tidak tersedia. Berikut hasil pencarian untuk '{$query}'.";
        }
    }
    
    /**
     * Get explanation from Google Gemini
     */
    private function getGeminiExplanation($query, $documents)
    {
        $apiKey = config('services.google_gemini.api_key');
        
        if (empty($apiKey)) {
            Log::warning('Google Gemini API key not configured');
            return $this->getFallbackExplanation($query, $documents);
        }
        
        $count = count($documents);
        $model = config('services.google_gemini.model', 'gemini-pro');
        $maxTokens = config('services.google_gemini.max_tokens', 150);
        $temperature = config('services.google_gemini.temperature', 0.7);
        $timeout = config('services.google_gemini.timeout', 10);
        
        // Prepare context from found documents
        $context = '';
        if ($count > 0) {
            $sampleTitles = $documents->pluck('title')->take(2)->implode(', ');
            $context = "Ditemukan {$count} dokumen terkait, contoh: {$sampleTitles}. ";
        }
        
        // Build prompt
        $prompt = "Anda adalah asisten AI untuk Jaringan Dokumentasi dan Informasi Hukum (JDIH) Kota Kendari. ";
        $prompt .= "Berikan penjelasan singkat (maksimal 3 kalimat) dalam bahasa Indonesia yang formal tentang: '{$query}' ";
        $prompt .= "dalam konteks dokumen hukum daerah. {$context}";
        $prompt .= "Jelaskan dengan bahasa yang mudah dipahami oleh masyarakat umum.";
        
        $client = new Client(['timeout' => $timeout]);
        
        try {
            $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => ['key' => $apiKey],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                        'topP' => config('services.google_gemini.top_p', 0.9),
                        'topK' => config('services.google_gemini.top_k', 40),
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ]
                    ]
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $explanation = trim($data['candidates'][0]['content']['parts'][0]['text']);
                Log::debug('Gemini explanation generated', ['length' => strlen($explanation)]);
                return $explanation;
            }
            
            throw new \Exception('Invalid response format from Gemini API');
            
        } catch (RequestException $e) {
            Log::warning('Gemini API Request Error', [
                'error' => $e->getMessage(),
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null
            ]);
        } catch (\Exception $e) {
            Log::warning('Gemini API Error', ['error' => $e->getMessage()]);
        }
        
        return $this->getFallbackExplanation($query, $documents);
    }
    
    /**
     * Get explanation from OpenAI
     */
    private function getOpenAIExplanation($query, $documents)
    {
        $apiKey = config('services.openai.api_key');
        
        if (empty($apiKey)) {
            Log::warning('OpenAI API key not configured');
            return $this->getFallbackExplanation($query, $documents);
        }
        
        $count = count($documents);
        $model = config('services.openai.model', 'gpt-3.5-turbo');
        $maxTokens = config('services.openai.max_tokens', 150);
        $temperature = config('services.openai.temperature', 0.7);
        $timeout = config('services.openai.timeout', 10);
        
        // Prepare context
        $context = $count > 0 ? "Ditemukan {$count} dokumen yang relevan. " : "";
        
        $client = new Client(['timeout' => $timeout]);
        
        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten AI untuk sistem dokumentasi hukum pemerintah daerah. Berikan penjelasan singkat, jelas, dan formal dalam bahasa Indonesia. Maksimal 3 kalimat.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Berikan penjelasan singkat tentang '{$query}' dalam konteks dokumen hukum daerah Kota Kendari. {$context}"
                        ]
                    ],
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'top_p' => 0.9,
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['choices'][0]['message']['content'])) {
                $explanation = trim($data['choices'][0]['message']['content']);
                Log::debug('OpenAI explanation generated', ['length' => strlen($explanation)]);
                return $explanation;
            }
            
            throw new \Exception('Invalid response format from OpenAI API');
            
        } catch (RequestException $e) {
            Log::warning('OpenAI API Request Error', [
                'error' => $e->getMessage(),
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null
            ]);
        } catch (\Exception $e) {
            Log::warning('OpenAI API Error', ['error' => $e->getMessage()]);
        }
        
        return $this->getFallbackExplanation($query, $documents);
    }
    
    /**
     * Get explanation from Azure OpenAI
     */
    private function getAzureExplanation($query, $documents)
    {
        $apiKey = config('services.azure_openai.api_key');
        $endpoint = config('services.azure_openai.endpoint');
        $deployment = config('services.azure_openai.deployment');
        
        if (empty($apiKey) || empty($endpoint) || empty($deployment)) {
            Log::warning('Azure OpenAI not fully configured');
            return $this->getFallbackExplanation($query, $documents);
        }
        
        $count = count($documents);
        $apiVersion = config('services.azure_openai.api_version', '2023-05-15');
        $timeout = config('services.azure_openai.timeout', 10);
        
        $client = new Client(['timeout' => $timeout]);
        
        try {
            $url = "{$endpoint}/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";
            
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'api-key' => $apiKey,
                ],
                'json' => [
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten AI untuk dokumentasi hukum. Berikan penjelasan singkat dalam bahasa Indonesia.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Jelaskan tentang '{$query}' dalam konteks dokumen hukum (ditemukan {$count} dokumen)."
                        ]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.7,
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['choices'][0]['message']['content'])) {
                return trim($data['choices'][0]['message']['content']);
            }
            
        } catch (\Exception $e) {
            Log::warning('Azure OpenAI Error', ['error' => $e->getMessage()]);
        }
        
        return $this->getFallbackExplanation($query, $documents);
    }
    
    /**
     * Fallback explanation when AI is not available
     */
    private function getFallbackExplanation($query, $documents)
    {
        $count = count($documents);
        
        if ($count > 0) {
            // Get unique categories and types
            $categories = $documents->pluck('category')->unique()->filter()->take(3);
            $types = $documents->pluck('type')->unique()->filter()->take(2);
            
            $explanation = "Berdasarkan pencarian untuk \"{$query}\", ditemukan {$count} dokumen hukum terkait di Kota Kendari. ";
            
            if ($categories->isNotEmpty()) {
                $explanation .= "Dokumen-dokumen ini termasuk dalam kategori: " . $categories->implode(', ') . ". ";
            }
            
            if ($types->isNotEmpty()) {
                $explanation .= "Jenis dokumen yang ditemukan: " . $types->implode(', ') . ". ";
            }
            
            $explanation .= "Hasil diurutkan berdasarkan relevansi dengan pencarian Anda.";
            
            return $explanation;
        }
        
        // Templates from config or default
        $templates = config('ai-search.explanations.templates', [
            'not_found' => "Pencarian untuk ':query' tidak menemukan dokumen yang spesifik. Disarankan untuk menggunakan kata kunci yang lebih umum atau konsultasi dengan bagian dokumentasi hukum."
        ]);
        
        return str_replace(':query', $query, $templates['not_found']);
    }
    
    /**
     * Check if table has FULLTEXT index
     */
    private function hasFulltextIndex()
    {
        try {
            $tableName = (new Document())->getTable();
            $result = DB::select("
                SHOW INDEX FROM {$tableName} 
                WHERE Index_type = 'FULLTEXT' 
                AND Key_name = 'document_search'
            ");
            return !empty($result);
        } catch (\Exception $e) {
            Log::warning('FULLTEXT index check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Get active provider name
     */
    private function getActiveProvider()
    {
        $provider = config('services.ai_search.provider', 'fallback');
        
        $providerNames = [
            'google_gemini' => 'Google Gemini AI',
            'openai' => 'OpenAI ChatGPT',
            'azure' => 'Azure OpenAI',
            'fallback' => 'System Fallback'
        ];
        
        return $providerNames[$provider] ?? 'Unknown';
    }
    
    /**
     * Simple search endpoint (no AI)
     */
    public function searchSimple(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:3|max:255'
        ]);
        
        $query = $validated['query'];
        
        $documents = $this->searchDocuments($query);
        $explanation = $this->getFallbackExplanation($query, $documents);
        
        return response()->json([
            'query' => $query,
            'explanation' => $explanation,
            'documents' => $documents,
            'total' => count($documents),
            'ai_used' => false,
            'provider' => 'Simple Search',
            'status' => 'success'
        ]);
    }
    
    /**
     * Health check for AI services
     */
    public function healthCheck()
    {
        $health = [
            'ai_search_enabled' => config('services.ai_search.enabled', true),
            'active_provider' => $this->getActiveProvider(),
            'database' => [
                'connected' => DB::connection()->getPdo() ? true : false,
                'table_exists' => Schema::hasTable('document'),
                'document_count' => Document::count(),
            ],
            'providers' => [
                'google_gemini' => !empty(config('services.google_gemini.api_key')),
                'openai' => !empty(config('services.openai.api_key')),
                'azure' => !empty(config('services.azure_openai.api_key')),
            ],
            'timestamp' => now()->toDateTimeString(),
        ];
        
        return response()->json($health);
    }
}