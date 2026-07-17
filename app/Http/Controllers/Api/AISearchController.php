<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AISearchRequest;
use App\Services\AISearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AISearchController extends Controller
{
    protected $aiSearchService;

    public function __construct(AISearchService $aiSearchService)
    {
        $this->aiSearchService = $aiSearchService;
    }

    /**
     * Handle AI search request
     */
    public function search(AISearchRequest $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $validated = $request->validated();
            
            $result = $this->aiSearchService->search(
                $validated['query'],
                $validated['filters'] ?? [],
                $validated['limit'] ?? 10
            );
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Log search (opsional)
            $this->logSearch($validated['query'], $responseTime, $result['success'], count($result['results']));
            
            return response()->json(array_merge($result, [
                'response_time' => $responseTime,
                'timestamp' => now()->toISOString(),
            ]));
            
        } catch (\Exception $e) {
            Log::error('AI Search Controller Error: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'results' => [],
                'suggestions' => [],
                'total' => 0,
                'response_time' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Get search suggestions without performing search
     */
    public function suggest(string $query): JsonResponse
    {
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'popular_searches' => $this->getPopularSearches()
            ]);
        }
        
        $suggestions = $this->generateQuerySuggestions($query);
        
        return response()->json([
            'query' => $query,
            'suggestions' => $suggestions,
            'popular_searches' => $this->getPopularSearches(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get search analytics (protected route)
     */
    public function analytics(): JsonResponse
    {
        // Hanya untuk admin atau dashboard
        $this->middleware('auth:api');
        
        $analytics = $this->aiSearchService->getAnalytics();
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'period' => 'last_30_days',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Health check endpoint for AI search service
     */
    public function health(): JsonResponse
    {
        $status = $this->checkServiceHealth();
        
        return response()->json([
            'service' => 'ai-search',
            'status' => $status['healthy'] ? 'healthy' : 'unhealthy',
            'database' => $status['database'],
            'memory_usage' => $this->getMemoryUsage(),
            'uptime' => $this->getUptime(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log search query (optional)
     */
    private function logSearch(string $query, float $responseTime, bool $success, int $resultCount): void
    {
        // Hanya log jika ada tabel search_logs
        if (\Schema::hasTable('search_logs')) {
            try {
                DB::table('search_logs')->insert([
                    'query' => $query,
                    'response_time' => $responseTime,
                    'success' => $success,
                    'result_count' => $resultCount,
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silent fail untuk logging
                Log::debug('Failed to log search: ' . $e->getMessage());
            }
        }
    }

    /**
     * Generate query suggestions
     */
    private function generateQuerySuggestions(string $query): array
    {
        $suggestions = [];
        
        // Suggestion berdasarkan pola umum
        $patterns = [
            'disabilitas' => [
                'peraturan disabilitas terbaru',
                'hak penyandang disabilitas',
                'aksesibilitas disabilitas',
            ],
            'perda' => [
                'perda tentang disabilitas',
                'perda hak disabilitas',
            ],
            'akses' => [
                'aksesibilitas fasilitas publik',
                'akses bangunan untuk disabilitas',
            ],
            'pendidikan' => [
                'pendidikan inklusif disabilitas',
                'sekolah untuk penyandang disabilitas',
            ],
        ];
        
        foreach ($patterns as $keyword => $patternSuggestions) {
            if (stripos($query, $keyword) !== false) {
                $suggestions = array_merge($suggestions, $patternSuggestions);
            }
        }
        
        // Tambahkan suggestion umum jika belum ada
        if (empty($suggestions)) {
            $suggestions = [
                'disabilitas ' . date('Y'),
                'peraturan daerah disabilitas',
                'keputusan wali kota disabilitas',
                'hak penyandang disabilitas di tempat kerja',
            ];
        }
        
        return array_slice(array_unique($suggestions), 0, 5);
    }

    /**
     * Get popular searches
     */
    private function getPopularSearches(): array
    {
        // Jika ada tabel search_logs, ambil dari sana
        if (\Schema::hasTable('search_logs')) {
            return DB::table('search_logs')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'query' => $item->query,
                        'count' => $item->count
                    ];
                })
                ->toArray();
        }
        
        // Default popular searches
        return [
            ['query' => 'peraturan disabilitas', 'count' => 1],
            ['query' => 'hak penyandang disabilitas', 'count' => 1],
            ['query' => 'aksesibilitas fasilitas publik', 'count' => 1],
        ];
    }

    /**
     * Check service health
     */
    private function checkServiceHealth(): array
    {
        $database = false;
        
        try {
            DB::connection()->getPdo();
            $database = true;
        } catch (\Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
        }
        
        return [
            'healthy' => $database,
            'database' => $database ? 'connected' : 'disconnected',
        ];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): string
    {
        $memory = memory_get_usage(true);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($memory, 1024));
        return round($memory / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    /**
     * Get server uptime
     */
    private function getUptime(): string
    {
        try {
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime !== false) {
                $uptime = explode(' ', $uptime)[0];
                $days = floor($uptime / 86400);
                $hours = floor(($uptime % 86400) / 3600);
                return $days . ' days, ' . $hours . ' hours';
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        
        return 'Unknown';
    }
}