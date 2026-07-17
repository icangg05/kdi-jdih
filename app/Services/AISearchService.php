<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AISearchService
{
    /**
     * Search documents using AI-enhanced query
     */
    public function search(string $query, array $filters = [], int $limit = 10): array
    {
        try {
            // Parse query untuk keyword dan intent
            $parsedQuery = $this->parseQuery($query);
            
            // Build base query
            $baseQuery = Document::query()
                ->with(['type', 'attachments'])
                ->where('status', 'published') // Hanya dokumen yang dipublikasi
                ->where(function ($q) use ($parsedQuery) {
                    // Search in title
                    $q->where('title', 'like', '%' . $parsedQuery['keywords'][0] . '%');
                    
                    // Search in content if available
                    if (isset($parsedQuery['keywords'][1])) {
                        $q->orWhere('content', 'like', '%' . $parsedQuery['keywords'][1] . '%');
                    }
                    
                    // Search in abstract/description
                    $q->orWhere('abstract', 'like', '%' . $parsedQuery['keywords'][0] . '%');
                });

            // Apply filters
            $baseQuery = $this->applyFilters($baseQuery, $filters, $parsedQuery);

            // Apply relevance scoring
            $results = $this->applyRelevanceScoring($baseQuery, $parsedQuery, $limit);

            // Get suggestions
            $suggestions = $this->generateSuggestions($parsedQuery, $results);

            return [
                'success' => true,
                'query' => $query,
                'parsed_query' => $parsedQuery,
                'results' => $results,
                'suggestions' => $suggestions,
                'total' => count($results),
                'search_time' => round(microtime(true) - LARAVEL_START, 3)
            ];

        } catch (\Exception $e) {
            Log::error('AI Search Error: ' . $e->getMessage(), [
                'query' => $query,
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan dalam pencarian',
                'results' => [],
                'suggestions' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Parse natural language query
     */
    private function parseQuery(string $query): array
    {
        $keywords = $this->extractKeywords($query);
        $intent = $this->detectIntent($query);
        $entities = $this->extractEntities($query);

        return [
            'original' => $query,
            'keywords' => $keywords,
            'intent' => $intent,
            'entities' => $entities,
            'has_year' => $this->extractYear($query),
            'has_number' => $this->extractDocumentNumber($query),
        ];
    }

    /**
     * Extract keywords from query
     */
    private function extractKeywords(string $query): array
    {
        // Hapus stop words umum dalam bahasa Indonesia
        $stopWords = ['tentang', 'dengan', 'untuk', 'pada', 'dalam', 'yang', 'dan', 'atau', 'di', 'ke', 'dari'];
        $words = preg_split('/\s+/', strtolower($query));
        
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        });

        // Tambahkan sinonim untuk kata kunci umum disabilitas
        $disabilitySynonyms = [
            'disabilitas' => ['difabel', 'penyandang cacat', 'cacat'],
            'aksesibilitas' => ['akses', 'kemudahan', 'fasilitas'],
            'pendidikan' => ['sekolah', 'belajar', 'inklusif'],
            'pekerjaan' => ['kerja', 'lapangan kerja', 'ketenagakerjaan'],
        ];

        $expandedKeywords = [];
        foreach ($keywords as $keyword) {
            $expandedKeywords[] = $keyword;
            if (isset($disabilitySynonyms[$keyword])) {
                $expandedKeywords = array_merge($expandedKeywords, $disabilitySynonyms[$keyword]);
            }
        }

        return array_unique($expandedKeywords);
    }

    /**
     * Detect user intent from query
     */
    private function detectIntent(string $query): string
    {
        $query = strtolower($query);
        
        if (str_contains($query, 'terbaru') || str_contains($query, 'baru')) {
            return 'latest';
        }
        
        if (str_contains($query, 'lama') || str_contains($query, 'tua')) {
            return 'oldest';
        }
        
        if (str_contains($query, 'perda') || str_contains($query, 'peraturan daerah')) {
            return 'perda';
        }
        
        if (str_contains($query, 'perwal') || str_contains($query, 'peraturan wali')) {
            return 'perwal';
        }
        
        if (str_contains($query, 'keputusan')) {
            return 'keputusan';
        }
        
        return 'general';
    }

    /**
     * Extract entities like document types, years, etc
     */
    private function extractEntities(string $query): array
    {
        $entities = [
            'document_types' => [],
            'years' => [],
            'numbers' => [],
        ];

        // Deteksi jenis dokumen
        $docTypePatterns = [
            'perda' => ['perda', 'peraturan daerah'],
            'perwal' => ['perwal', 'peraturan wali kota'],
            'kepwal' => ['kepwal', 'keputusan wali kota'],
            'surat_edaran' => ['surat edaran', 'se'],
        ];

        foreach ($docTypePatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (stripos($query, $pattern) !== false) {
                    $entities['document_types'][] = $type;
                    break;
                }
            }
        }

        // Deteksi tahun (4 digit)
        preg_match_all('/\b(19|20)\d{2}\b/', $query, $yearMatches);
        if (!empty($yearMatches[0])) {
            $entities['years'] = $yearMatches[0];
        }

        // Deteksi nomor dokumen
        preg_match_all('/\bno\.?\s*(\d+)\b/i', $query, $numberMatches);
        if (!empty($numberMatches[1])) {
            $entities['numbers'] = $numberMatches[1];
        }

        return $entities;
    }

    /**
     * Extract year from query
     */
    private function extractYear(string $query): ?int
    {
        preg_match('/\b(19|20)\d{2}\b/', $query, $matches);
        return !empty($matches) ? (int) $matches[0] : null;
    }

    /**
     * Extract document number from query
     */
    private function extractDocumentNumber(string $query): ?string
    {
        preg_match('/\bno\.?\s*(\d+)\b/i', $query, $matches);
        return !empty($matches[1]) ? $matches[1] : null;
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters, array $parsedQuery)
    {
        // Filter berdasarkan tahun dari query atau filter
        if (!empty($parsedQuery['has_year'])) {
            $query->whereYear('enactment_date', $parsedQuery['has_year']);
        } elseif (!empty($filters['year'])) {
            $query->whereYear('enactment_date', $filters['year']);
        }

        // Filter berdasarkan nomor dokumen
        if (!empty($parsedQuery['has_number'])) {
            $query->where('document_number', 'like', '%' . $parsedQuery['has_number'] . '%');
        }

        // Filter berdasarkan kategori dari query atau filter
        if (!empty($parsedQuery['entities']['document_types'])) {
            $documentTypeIds = DocumentType::whereIn('code', $parsedQuery['entities']['document_types'])
                ->pluck('id')
                ->toArray();
            
            if (!empty($documentTypeIds)) {
                $query->whereIn('document_type_id', $documentTypeIds);
            }
        } elseif (!empty($filters['category'])) {
            $query->whereHas('type', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // Filter berdasarkan tipe dokumen
        if (!empty($filters['type'])) {
            $query->whereHas('type', function ($q) use ($filters) {
                $q->where('code', $filters['type']);
            });
        }

        return $query;
    }

    /**
     * Apply relevance scoring to results
     */
    private function applyRelevanceScoring($query, array $parsedQuery, int $limit)
    {
        $results = $query->get()->map(function ($document) use ($parsedQuery) {
            $score = 0;
            
            // Skor berdasarkan kecocokan judul
            $titleScore = $this->calculateTitleScore($document->title, $parsedQuery['keywords']);
            $score += $titleScore * 40; // 40% weight
            
            // Skor berdasarkan konten/abstrak
            $contentScore = $this->calculateContentScore($document->abstract ?? '', $parsedQuery['keywords']);
            $score += $contentScore * 30; // 30% weight
            
            // Skor berdasarkan tahun (dokumen terbaru lebih relevan)
            if ($document->enactment_date) {
                $yearScore = $this->calculateYearScore($document->enactment_date);
                $score += $yearScore * 15; // 15% weight
            }
            
            // Skor berdasarkan jenis dokumen (jika ada intent spesifik)
            $typeScore = $this->calculateTypeScore($document->type->code ?? '', $parsedQuery['intent']);
            $score += $typeScore * 15; // 15% weight
            
            // Normalize score to 0-100
            $normalizedScore = min(100, max(0, $score));
            
            return [
                'id' => $document->id,
                'title' => $document->title,
                'document_number' => $document->document_number,
                'year' => $document->enactment_date ? $document->enactment_date->format('Y') : null,
                'type' => $document->type->name ?? 'Tidak diketahui',
                'type_code' => $document->type->code ?? '',
                'abstract' => $document->abstract,
                'enactment_date' => $document->enactment_date ? $document->enactment_date->format('d/m/Y') : null,
                'file_url' => $document->attachments->first()->file_path ?? null,
                'relevance_score' => round($normalizedScore),
                'relevance_color' => $this->getRelevanceColor($normalizedScore),
                'excerpt' => $this->generateExcerpt($document->abstract ?? '', $parsedQuery['keywords']),
            ];
        });

        // Sort by relevance score descending
        return $results->sortByDesc('relevance_score')->take($limit)->values()->toArray();
    }

    /**
     * Calculate title score
     */
    private function calculateTitleScore(string $title, array $keywords): float
    {
        $titleLower = strtolower($title);
        $score = 0;
        
        foreach ($keywords as $keyword) {
            if (str_contains($titleLower, strtolower($keyword))) {
                $score += 1;
            }
        }
        
        return min(1, $score / count($keywords));
    }

    /**
     * Calculate content score
     */
    private function calculateContentScore(string $content, array $keywords): float
    {
        if (empty($content)) return 0;
        
        $contentLower = strtolower($content);
        $score = 0;
        
        foreach ($keywords as $keyword) {
            $count = substr_count($contentLower, strtolower($keyword));
            $score += min(1, $count * 0.2); // Maksimal 1 per keyword
        }
        
        return min(1, $score / count($keywords));
    }

    /**
     * Calculate year score (newer documents get higher score)
     */
    private function calculateYearScore($date): float
    {
        if (!$date) return 0.5;
        
        $year = $date->format('Y');
        $currentYear = date('Y');
        $age = $currentYear - $year;
        
        // Dokumen dari 5 tahun terakhir mendapat skor tinggi
        if ($age <= 5) return 1.0;
        if ($age <= 10) return 0.7;
        if ($age <= 20) return 0.4;
        return 0.1;
    }

    /**
     * Calculate type score based on intent
     */
    private function calculateTypeScore(string $docType, string $intent): float
    {
        $typeMapping = [
            'perda' => ['perda' => 1.0, 'general' => 0.7],
            'perwal' => ['perwal' => 1.0, 'general' => 0.6],
            'kepwal' => ['keputusan' => 1.0, 'general' => 0.6],
            'surat_edaran' => ['general' => 0.5],
        ];
        
        return $typeMapping[$docType][$intent] ?? 0.3;
    }

    /**
     * Get relevance color based on score
     */
    private function getRelevanceColor(float $score): string
    {
        if ($score >= 80) return 'bg-green-100 text-green-800';
        if ($score >= 60) return 'bg-blue-100 text-blue-800';
        if ($score >= 40) return 'bg-yellow-100 text-yellow-800';
        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Generate excerpt with highlighted keywords
     */
    private function generateExcerpt(string $text, array $keywords, int $length = 150): string
    {
        if (empty($text)) return 'Tidak ada deskripsi tersedia.';
        
        foreach ($keywords as $keyword) {
            $pos = stripos($text, $keyword);
            if ($pos !== false) {
                $start = max(0, $pos - 30);
                $excerpt = substr($text, $start, $length);
                
                // Highlight keywords in excerpt
                foreach ($keywords as $kw) {
                    $excerpt = preg_replace("/\b($kw)\b/i", '<strong>$1</strong>', $excerpt);
                }
                
                return (($start > 0) ? '...' : '') . $excerpt . (strlen($text) > ($start + $length) ? '...' : '');
            }
        }
        
        // Jika tidak ditemukan keyword, ambil potongan awal
        $excerpt = substr($text, 0, $length);
        return strlen($text) > $length ? $excerpt . '...' : $excerpt;
    }

    /**
     * Generate search suggestions
     */
    private function generateSuggestions(array $parsedQuery, array $results): array
    {
        $suggestions = [];
        
        // Suggestion berdasarkan intent
        if ($parsedQuery['intent'] === 'general') {
            $suggestions[] = [
                'text' => 'Coba tambahkan tahun untuk hasil lebih spesifik',
                'type' => 'tip'
            ];
        }
        
        // Suggestion berdasarkan jumlah hasil
        if (count($results) === 0) {
            $suggestions[] = [
                'text' => 'Coba gunakan kata kunci yang lebih umum',
                'type' => 'tip'
            ];
            
            // Suggestion dokumen populer terkait disabilitas
            $suggestions[] = [
                'text' => 'Lihat dokumen populer tentang disabilitas',
                'query' => 'dokumen disabilitas terbaru',
                'type' => 'query'
            ];
        } elseif (count($results) < 5) {
            $suggestions[] = [
                'text' => 'Coba hapus filter untuk mendapatkan lebih banyak hasil',
                'type' => 'tip'
            ];
        }
        
        // Suggestion berdasarkan keyword
        if (count($parsedQuery['keywords']) === 1) {
            $suggestions[] = [
                'text' => 'Coba tambahkan kata kunci tambahan untuk hasil lebih akurat',
                'type' => 'tip'
            ];
        }
        
        // Suggestion dokumen terkait
        if (!empty($results)) {
            $topResultType = $results[0]['type_code'] ?? '';
            if ($topResultType) {
                $suggestions[] = [
                    'text' => "Lihat semua dokumen jenis {$results[0]['type']}",
                    'query' => "semua {$topResultType} disabilitas",
                    'type' => 'query'
                ];
            }
        }
        
        return $suggestions;
    }

    /**
     * Get search analytics (for dashboard)
     */
    public function getAnalytics(): array
    {
        $popularSearches = DB::table('search_logs')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'popular_searches' => $popularSearches,
            'total_searches' => DB::table('search_logs')->whereDate('created_at', '>=', now()->subDays(30))->count(),
            'avg_response_time' => DB::table('search_logs')->whereDate('created_at', '>=', now()->subDays(30))->avg('response_time') ?? 0,
        ];
    }
}