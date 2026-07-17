<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Helper functions dengan error handling
            function safeSum($table, $column, $default = 0) {
                try {
                    return DB::table($table)->sum($column) ?? $default;
                } catch (\Exception $e) {
                    return $default;
                }
            }

            function safeCount($table, $conditions = []) {
                try {
                    $query = DB::table($table);
                    foreach ($conditions as $column => $value) {
                        if (is_string($column)) {
                            $query->where($column, $value);
                        }
                    }
                    return $query->count();
                } catch (\Exception $e) {
                    return 0;
                }
            }

            function safeSelect($table, $columns, $groupBy = null, $where = []) {
                try {
                    $query = DB::table($table)->select($columns);
                    
                    foreach ($where as $column => $value) {
                        if (is_string($column)) {
                            $query->where($column, $value);
                        }
                    }
                    
                    if ($groupBy && is_string($groupBy)) {
                        $query->groupBy($groupBy);
                    }
                    
                    return $query->get();
                } catch (\Exception $e) {
                    return collect([]);
                }
            }

            // ==================== STATISTIK UTAMA ====================
            
            // A. Statistik Dokumen
            $countPeraturan = safeCount('document', ['tipe_dokumen' => 1]);
            $countMonografi = safeCount('document', ['tipe_dokumen' => 2]);
            $countArtikel = safeCount('document', ['tipe_dokumen' => 3]);
            $countPutusan = safeCount('document', ['tipe_dokumen' => 4]);
            $countPembentukan = safeCount('pembentukan_puu');
            $countDisabilitas = safeCount('disabilitas');

            // B. Total Dokumen Hukum
            $totalDokumenHukum = $countPeraturan + $countMonografi + $countArtikel + $countPutusan 
                                + $countPembentukan + $countDisabilitas;

            // C. Total Koleksi PUU
            $totalKoleksiPUU = $countPeraturan + $countPembentukan;
            
            // D. Status Keberlakuan PUU
            $statusKeberlakuan = safeSelect('document', 
                [DB::raw('status as status'), DB::raw('COUNT(*) as total')],
                'status',
                ['tipe_dokumen' => 1]
            );
            
            $statusPembentukan = safeSelect('pembentukan_puu',
                [DB::raw('status_dokumen as status'), DB::raw('COUNT(*) as total')],
                'status_dokumen'
            );
            
            $statusKeberlakuan = $statusKeberlakuan->concat($statusPembentukan);

            // E. Jenis PUU
            $jenisDocument = safeSelect('document',
                [DB::raw('jenis_peraturan as jenis_peraturan'), DB::raw('COUNT(*) as total')],
                'jenis_peraturan',
                ['tipe_dokumen' => 1]
            );

            $jenisPembentukan = safeSelect('pembentukan_puu',
                [DB::raw('jenis_dokumen as jenis_peraturan'), DB::raw('COUNT(*) as total')],
                'jenis_dokumen'
            );

            $jenisPUU = $jenisDocument->concat($jenisPembentukan);

            // F. Statistik Akses & Download
            $totalAkses = safeSum('document', 'hit_see')
                        + safeSum('pembentukan_puu', 'views')
                        + safeSum('disabilitas', 'views');

            $totalDownload = safeSum('document', 'hit_download')
                           + safeSum('pembentukan_puu', 'jumlah_download')
                           + safeSum('disabilitas', 'jumlah_download');

            // ==================== STATISTIK DETAIL ====================
            
            // G. Statistik Pembentukan PUU
            $statistikPembentukan = [
                'total' => $countPembentukan,
                'by_tahapan' => safeSelect('pembentukan_puu',
                    [DB::raw('tahapan_pembentukan as tahapan'), DB::raw('COUNT(*) as total')],
                    'tahapan_pembentukan'
                ),
                'by_status' => safeSelect('pembentukan_puu',
                    [DB::raw('status_dokumen as status'), DB::raw('COUNT(*) as total')],
                    'status_dokumen'
                ),
                'by_jenis_rancangan' => safeSelect('pembentukan_puu',
                    [DB::raw('jenis_rancangan as jenis'), DB::raw('COUNT(*) as total')],
                    'jenis_rancangan'
                ),
                'by_prolegnas' => safeSelect('pembentukan_puu',
                    [DB::raw('prolegnas as prolegnas'), DB::raw('COUNT(*) as total')],
                    'prolegnas'
                ),
                'recent_uploads' => function() {
                    try {
                        return DB::table('pembentukan_puu')
                            ->select('judul', 'created_at')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    } catch (\Exception $e) {
                        return collect([]);
                    }
                },
            ];

            // H. Statistik Disabilitas
            $statistikDisabilitas = [
                'total' => $countDisabilitas,
                'by_jenis_disabilitas' => safeSelect('disabilitas',
                    [DB::raw('jenis_disabilitas as jenis'), DB::raw('COUNT(*) as total')],
                    'jenis_disabilitas'
                ),
                'by_status' => safeSelect('disabilitas',
                    [DB::raw('status_dokumen as status'), DB::raw('COUNT(*) as total')],
                    'status_dokumen'
                ),
                'by_sektor' => safeSelect('disabilitas',
                    [DB::raw('sektor_kebijakan as sektor'), DB::raw('COUNT(*) as total')],
                    'sektor_kebijakan'
                ),
                'by_tahun' => safeSelect('disabilitas',
                    [DB::raw('tahun as tahun'), DB::raw('COUNT(*) as total')],
                    'tahun'
                ),
                'recent_uploads' => function() {
                    try {
                        return DB::table('disabilitas')
                            ->select('judul', 'created_at')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    } catch (\Exception $e) {
                        return collect([]);
                    }
                },
            ];

            // I. Statistik Timeline
            $currentYear = date('Y');
            $startYear = $currentYear - 4;
            $years = range($startYear, $currentYear);
            
            $peraturanData = [];
            $monografiData = [];
            $putusanData = [];
            $pembentukanData = [];
            $disabilitasData = [];

            foreach ($years as $year) {
                // Dari tabel document - peraturan
                try {
                    $peraturanData[] = DB::table('document')
                        ->where('tipe_dokumen', 1)
                        ->whereYear('created_at', $year)
                        ->count();
                } catch (\Exception $e) {
                    $peraturanData[] = 0;
                }
                
                // Dari tabel document - monografi
                try {
                    $monografiData[] = DB::table('document')
                        ->where('tipe_dokumen', 2)
                        ->whereYear('created_at', $year)
                        ->count();
                } catch (\Exception $e) {
                    $monografiData[] = 0;
                }
                
                // Dari tabel document - putusan
                try {
                    $putusanData[] = DB::table('document')
                        ->where('tipe_dokumen', 4)
                        ->whereYear('created_at', $year)
                        ->count();
                } catch (\Exception $e) {
                    $putusanData[] = 0;
                }
                
                // Dari tabel pembentukan_puu
                try {
                    $pembentukanData[] = DB::table('pembentukan_puu')
                        ->whereYear('created_at', $year)
                        ->count();
                } catch (\Exception $e) {
                    $pembentukanData[] = 0;
                }
                
                // Dari tabel disabilitas
                try {
                    $disabilitasData[] = DB::table('disabilitas')
                        ->whereYear('created_at', $year)
                        ->count();
                } catch (\Exception $e) {
                    $disabilitasData[] = 0;
                }
            }

            // J. Statistik Survei - DIPERBAIKI
            $statistikSurvei = $this->getStatistikSurveiData();

            // 9. Siapkan data untuk chart
            $statusLabels = $statusKeberlakuan->pluck('status')->map(function($item) {
                return $item ?: 'Tidak Terdefinisi';
            })->toArray();
            $statusChartData = $statusKeberlakuan->pluck('total')->toArray();
            $statusColors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1'];

            $jenisLabels = $jenisPUU->pluck('jenis_peraturan')->map(function($item) {
                return $item ?: 'Lainnya';
            })->toArray();
            $jenisChartData = $jenisPUU->pluck('total')->toArray();

            // Execute function calls
            $statistikPembentukan['recent_uploads'] = $statistikPembentukan['recent_uploads']();
            $statistikDisabilitas['recent_uploads'] = $statistikDisabilitas['recent_uploads']();

            return view('backend.dashboard', compact(
                // Dokumen
                'countPeraturan',
                'countMonografi',
                'countArtikel',
                'countPutusan',
                'countPembentukan',
                'countDisabilitas',
                
                // Total
                'totalDokumenHukum',
                'totalKoleksiPUU',
                
                // Statistik
                'statusKeberlakuan',
                'jenisPUU',
                'totalAkses',
                'totalDownload',
                
                // Detail
                'statistikPembentukan',
                'statistikDisabilitas',
                'statistikSurvei',
                
                // Timeline
                'years',
                'peraturanData',
                'monografiData',
                'putusanData',
                'pembentukanData',
                'disabilitasData',
                
                // Chart Data
                'statusLabels',
                'statusChartData',
                'statusColors',
                'jenisLabels',
                'jenisChartData'
            ));

        } catch (\Exception $e) {
            // Fallback minimal
            return view('backend.dashboard', $this->getFallbackData());
        }
    }

    /**
     * Mendapatkan data statistik survei
     */
    private function getStatistikSurveiData()
    {
        try {
            // Cek jika tabel surveys ada
            if (!Schema::hasTable('surveys')) {
                return $this->getDefaultStatistikSurvei();
            }
            
            // Menggunakan data langsung dari database
            $total = DB::table('surveys')->count();
            
            // Perhatikan: field name harus sesuai dengan SurveyController Anda
            $averageKemudahan = DB::table('surveys')->avg('kemudahan_akses') ?? 0;
            $averageKelengkapan = DB::table('surveys')->avg('kelengkapan_informasi') ?? 0;
            $averageKecepatan = DB::table('surveys')->avg('kecepatan_loading') ?? 0;
            $averageTampilan = DB::table('surveys')->avg('tampilan_antarmuka') ?? 0;
            $averageRelevansi = DB::table('surveys')->avg('relevansi_pencarian') ?? 0;
            
            $overallAverage = ($averageKemudahan + $averageKelengkapan + $averageKecepatan + 
                             $averageTampilan + $averageRelevansi) / 5;
            
            $jenisPengguna = DB::table('surveys')
                ->select('jenis_pengguna', DB::raw('COUNT(*) as total'))
                ->groupBy('jenis_pengguna')
                ->get();
            
            $recentSurveys = DB::table('surveys')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Distribusi rating
            $ratingDistribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $ratingDistribution[$i] = [
                    'kemudahan' => DB::table('surveys')->where('kemudahan_akses', $i)->count(),
                    'kelengkapan' => DB::table('surveys')->where('kelengkapan_informasi', $i)->count(),
                    'kecepatan' => DB::table('surveys')->where('kecepatan_loading', $i)->count(),
                    'tampilan' => DB::table('surveys')->where('tampilan_antarmuka', $i)->count(),
                    'relevansi' => DB::table('surveys')->where('relevansi_pencarian', $i)->count(),
                ];
            }
            
            return [
                'total' => $total,
                'average_kemudahan' => round($averageKemudahan, 1),
                'average_kelengkapan' => round($averageKelengkapan, 1),
                'average_kecepatan' => round($averageKecepatan, 1),
                'average_tampilan' => round($averageTampilan, 1),
                'average_relevansi' => round($averageRelevansi, 1),
                'average_overall' => round($overallAverage, 1),
                'jenis_pengguna' => $jenisPengguna,
                'recent_surveys' => $recentSurveys,
                'rating_distribution' => $ratingDistribution,
            ];
            
        } catch (\Exception $e) {
            return $this->getDefaultStatistikSurvei();
        }
    }
    
    /**
     * Data default untuk statistik survei
     */
    private function getDefaultStatistikSurvei()
    {
        return [
            'total' => 0,
            'average_kemudahan' => 0,
            'average_kelengkapan' => 0,
            'average_kecepatan' => 0,
            'average_tampilan' => 0,
            'average_relevansi' => 0,
            'average_overall' => 0,
            'jenis_pengguna' => collect([]),
            'recent_surveys' => collect([]),
            'rating_distribution' => [],
        ];
    }

    private function getFallbackData()
    {
        $years = range(date('Y')-4, date('Y'));
        
        return [
            'countPeraturan' => 0,
            'countMonografi' => 0,
            'countArtikel' => 0,
            'countPutusan' => 0,
            'countPembentukan' => 0,
            'countDisabilitas' => 0,
            'totalDokumenHukum' => 0,
            'totalKoleksiPUU' => 0,
            'statusKeberlakuan' => collect([]),
            'jenisPUU' => collect([]),
            'totalAkses' => 0,
            'totalDownload' => 0,
            'statistikPembentukan' => [
                'total' => 0, 
                'by_tahapan' => collect([]),
                'recent_uploads' => collect([])
            ],
            'statistikDisabilitas' => [
                'total' => 0, 
                'by_jenis_disabilitas' => collect([]),
                'recent_uploads' => collect([])
            ],
            'statistikSurvei' => $this->getDefaultStatistikSurvei(),
            'years' => $years,
            'peraturanData' => array_fill(0, 5, 0),
            'monografiData' => array_fill(0, 5, 0),
            'putusanData' => array_fill(0, 5, 0),
            'pembentukanData' => array_fill(0, 5, 0),
            'disabilitasData' => array_fill(0, 5, 0),
            'statusLabels' => [],
            'statusChartData' => [],
            'statusColors' => [],
            'jenisLabels' => [],
            'jenisChartData' => [],
        ];
    }

    // ==================== METHOD TAMBAHAN ====================

    public function getAktivitasTerbaru()
    {
        try {
            $aktivitasDocument = DB::table('document')
                ->select('judul', 'created_at', DB::raw("'Document' as tipe"))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $aktivitasPembentukan = DB::table('pembentukan_puu')
                ->select('judul', 'created_at', DB::raw("'Pembentukan PUU' as tipe"))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $aktivitasDisabilitas = DB::table('disabilitas')
                ->select('judul', 'created_at', DB::raw("'Disabilitas' as tipe"))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Hanya ambil aktivitas survei jika tabel ada
            $aktivitasSurvei = collect([]);
            if (Schema::hasTable('surveys')) {
                $aktivitasSurvei = DB::table('surveys')
                    ->select('nama as judul', 'created_at', DB::raw("'Survei' as tipe"))
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }
            
            $allAktivitas = $aktivitasDocument
                ->concat($aktivitasPembentukan)
                ->concat($aktivitasDisabilitas)
                ->concat($aktivitasSurvei)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => $allAktivitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getDashboardSummary()
    {
        try {
            $summary = [
                'dokumen' => [
                    'peraturan' => DB::table('document')->where('tipe_dokumen', 1)->count(),
                    'monografi' => DB::table('document')->where('tipe_dokumen', 2)->count(),
                    'artikel' => DB::table('document')->where('tipe_dokumen', 3)->count(),
                    'putusan' => DB::table('document')->where('tipe_dokumen', 4)->count(),
                    'pembentukan' => DB::table('pembentukan_puu')->count(),
                    'disabilitas' => DB::table('disabilitas')->count(),
                ],
                'total_dokumen' => DB::table('document')->count() + 
                                 DB::table('pembentukan_puu')->count() + 
                                 DB::table('disabilitas')->count(),
                'total_survei' => Schema::hasTable('surveys') ? DB::table('surveys')->count() : 0,
                'today_activity' => [
                    'dokumen' => DB::table('document')->whereDate('created_at', today())->count(),
                    'survei' => Schema::hasTable('surveys') ? DB::table('surveys')->whereDate('created_at', today())->count() : 0,
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    // Method lainnya tetap sama dengan error handling
    public function exportDatabase()
    {
        try {
            $dbName   = env('DB_DATABASE');
            $user     = env('DB_USERNAME');
            $pass     = env('DB_PASSWORD');
            $host     = env('DB_HOST');
            $filename = 'backup_' . $dbName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $filePath = storage_path("app/public/$filename");

            $mysqldump = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
            $passPart = $pass ? "-p$pass" : '';
            $command  = "\"$mysqldump\" -h $host -u $user $passPart $dbName > \"$filePath\"";

            exec($command . ' 2>&1', $output, $result);

            if (!file_exists($filePath) || filesize($filePath) < 5000) {
              return response()->json([
                'status' => 'error',
                'message' => 'Dump gagal atau hanya ekspor struktur.',
                'command' => $command,
                'output' => $output,
                'exit_code' => $result,
                'filesize' => filesize($filePath)
              ], 500);
            }

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile(Request $request)
    {
        try {
            return Storage::download($request->filePath);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function generateWilayah()
    {
        try {
            $response = Http::timeout(5)->get('https://wilayah.id/api/provinces.json');

            if (!$response->successful()) {
              return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi'
              ], 500);
            }

            $result = $response->json()['data'] ?? [];

            $provinsiList = collect($result)
              ->map(fn($item) => [
                'code' => $item['code'],
                'label' => strtoupper($item['name']),
                'value' => strtoupper($item['name'])
              ])
              ->values()
              ->toArray();

            $gabunganWilayah = $provinsiList;

            foreach ($provinsiList as $prov) {
              $code = $prov['code'];
              $kabResponse = Http::timeout(5)->get("https://wilayah.id/api/regencies/{$code}.json");

              if ($kabResponse->successful()) {
                $kabupatenList = $kabResponse->json()['data'] ?? [];

                $formattedKabupaten = collect($kabupatenList)
                  ->map(fn($item) => [
                    'label' => strtoupper($item['name']),
                    'value' => strtoupper($item['name'])
                  ])
                  ->values()
                  ->toArray();

                $gabunganWilayah = array_merge($gabunganWilayah, $formattedKabupaten);
              }
            }

            Storage::put('data_wilayah.json', json_encode($gabunganWilayah, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
              'success' => true,
              'message' => 'Data provinsi dan kabupaten berhasil disimpan',
              'total_data' => count($gabunganWilayah)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}