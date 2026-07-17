<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JdihKendariApiController extends Controller
{
    // ==============================================
    // 1. HEALTH CHECK
    // ==============================================
    public function health()
    {
        return response()->json([
            'status' => 'success',
            'service' => 'JDIH Kota Kendari API',
            'version' => '1.0.0',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'database' => 'connected',
            'total_documents' => DB::table('document')->count(),
            'environment' => app()->environment()
        ]);
    }
    
    public function healthCheck()
    {
        return $this->health();
    }
    
    // ==============================================
    // 2. SEARCH
    // ==============================================
    public function search(Request $request)
    {
        // Simple API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required. Use X-API-Key header with jdih_ prefix'
            ], 401);
        }
        
        try {
            // Parameters
            $query = $request->get('q', '');
            $limit = (int) $request->get('limit', 20);
            $page = max($request->get('page', 1), 1);
            $offset = ($page - 1) * $limit;
            
            // Query utama
            $documentsQuery = DB::table('document')
                ->select([
                    'id',
                    'judul',
                    'nomor_peraturan',
                    'tahun_terbit',
                    'jenis_peraturan',
                    'singkatan_jenis',
                    'status',
                    'abstrak',
                    'bidang_hukum',
                    'hit_see',
                    'hit_download',
                    'created_at',
                    'updated_at'
                ]);
            
            // Filter pencarian
            if (!empty($query)) {
                $documentsQuery->where(function($q) use ($query) {
                    $q->where('judul', 'like', "%{$query}%")
                      ->orWhere('nomor_peraturan', 'like', "%{$query}%")
                      ->orWhere('abstrak', 'like', "%{$query}%")
                      ->orWhere('bidang_hukum', 'like', "%{$query}%");
                });
            }
            
            // Filter tahun
            if ($request->has('tahun')) {
                $documentsQuery->where('tahun_terbit', $request->tahun);
            }
            
            // Filter status
            if ($request->has('status')) {
                $documentsQuery->where('status', $request->status);
            }
            
            // Filter bidang hukum
            if ($request->has('bidang_hukum')) {
                $documentsQuery->where('bidang_hukum', 'like', "%{$request->bidang_hukum}%");
            }
            
            // Sorting - default terbaru
            $documentsQuery->orderBy('created_at', 'desc');
            
            // Get total count
            $total = $documentsQuery->count();
            
            // Get paginated results
            if ($limit > 0) {
                $documents = $documentsQuery->skip($offset)->take($limit)->get();
            } else {
                $documents = $documentsQuery->get();
                $limit = $total;
            }
            
            // Format response
            $formattedDocs = $documents->map(function ($doc) {
                $fileInfo = $this->getDocumentFileInfo($doc->id);
                
                return [
                    'id' => $doc->id,
                    'judul' => $doc->judul ?? 'Tidak ada judul',
                    'nomor_peraturan' => $doc->nomor_peraturan ?? '-',
                    'tahun_terbit' => $doc->tahun_terbit ?? date('Y'),
                    'tipe_dokumen_nama' => $doc->jenis_peraturan ?? 'PERATURAN DAERAH',
                    'tipe_dokumen_singkatan' => $doc->singkatan_jenis ?? 'PERDA',
                    'status' => $doc->status ?? 'Berlaku',
                    'abstrak' => $this->truncateText($doc->abstrak ?? '', 200),
                    'bidang_hukum' => $doc->bidang_hukum ?? 'Umum',
                    'hit_see' => $doc->hit_see ?? 0,
                    'hit_download' => $doc->hit_download ?? 0,
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at,
                    'detail_url' => url("/api/jdih/documents/{$doc->id}"),
                    'download_url' => $fileInfo['download_url'],
                    'has_file' => $fileInfo['has_file']
                ];
            });
            
            // Return langsung array data tanpa wrapper 'success'
            if ($formattedDocs->isEmpty()) {
                return response()->json([]);
            }
            
            return response()->json($formattedDocs);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // ==============================================
    // 3. DOCUMENT DETAIL
    // ==============================================
    public function show($id, Request $request)
    {
        // API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required'
            ], 401);
        }
        
        try {
            $document = DB::table('document')
                ->where('id', $id)
                ->first();
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }
            
            // Get file information
            $fileInfo = $this->getDocumentFileInfo($id);
            
            // Return langsung array data tanpa wrapper 'success'
            return response()->json([
                'id' => $document->id,
                'judul' => $document->judul,
                'nomor_peraturan' => $document->nomor_peraturan,
                'tahun_terbit' => $document->tahun_terbit,
                'tipe_dokumen' => [
                    'nama' => $document->jenis_peraturan ?? 'PERATURAN DAERAH',
                    'singkatan' => $document->singkatan_jenis ?? 'PERDA'
                ],
                'status' => $document->status ?? 'Berlaku',
                'abstrak' => $document->abstrak ?? '',
                'bidang_hukum' => $document->bidang_hukum ?? 'Umum',
                'tempat_penetapan' => $document->tempat_terbit ?? 'Kendari',
                'tanggal_penetapan' => $document->tanggal_penetapan,
                'tanggal_pengundangan' => $document->tanggal_pengundangan,
                'penandatanganan' => $document->penandatanganan ?? 'Wali Kota Kendari',
                'penerbit' => $document->penerbit ?? 'Pemerintah Kota Kendari',
                'bahasa' => $document->bahasa ?? 'Indonesia',
                'statistik' => [
                    'dilihat' => $document->hit_see ?? 0,
                    'diunduh' => $document->hit_download ?? 0
                ],
                'file_information' => $fileInfo,
                'related_documents' => [],
                'metadata' => [
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // ==============================================
    // 4. DOCUMENT DOWNLOAD
    // ==============================================
    public function download($id, Request $request)
    {
        // API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required'
            ], 401);
        }
        
        try {
            $document = DB::table('document')
                ->where('id', $id)
                ->select('id', 'judul', 'hit_download')
                ->first();
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }
            
            // Update download counter
            DB::table('document')
                ->where('id', $id)
                ->increment('hit_download', 1);
            
            // Cari file di tabel data_lampiran
            $fileInfo = $this->getDocumentFileInfo($id);
            
            // Return langsung array data tanpa wrapper 'success'
            return response()->json([
                'id' => $document->id,
                'judul' => $document->judul,
                'download_count' => ($document->hit_download ?? 0) + 1,
                'file_info' => $fileInfo,
                'instructions' => [
                    '1. File tersedia dalam format PDF',
                    '2. Gunakan link di atas untuk mengunduh',
                    '3. API key diperlukan untuk akses'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // ==============================================
    // 5. STATISTICS
    // ==============================================
    public function statistics(Request $request)
    {
        // API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required'
            ], 401);
        }
        
        try {
            // Total dokumen
            $totalDocs = DB::table('document')->count();
            
            // Total views & downloads
            $totalViews = DB::table('document')->sum('hit_see') ?? 0;
            $totalDownloads = DB::table('document')->sum('hit_download') ?? 0;
            
            // Dokumen per tahun
            $perYear = DB::table('document')
                ->select(DB::raw('tahun_terbit as tahun'), DB::raw('COUNT(*) as total'))
                ->whereNotNull('tahun_terbit')
                ->groupBy('tahun_terbit')
                ->orderBy('tahun_terbit', 'desc')
                ->limit(10)
                ->get();
            
            // Dokumen per tipe
            $perType = DB::table('document')
                ->select('jenis_peraturan as tipe', DB::raw('COUNT(*) as total'))
                ->whereNotNull('jenis_peraturan')
                ->groupBy('jenis_peraturan')
                ->orderBy('total', 'desc')
                ->get();
            
            // Dokumen per status
            $perStatus = DB::table('document')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->whereNotNull('status')
                ->groupBy('status')
                ->orderBy('total', 'desc')
                ->get();
            
            // Most viewed
            $mostViewed = DB::table('document')
                ->select('id', 'judul', 'hit_see as views')
                ->where('hit_see', '>', 0)
                ->orderBy('hit_see', 'desc')
                ->limit(5)
                ->get();
            
            // Return langsung array data tanpa wrapper 'success'
            return response()->json([
                'total_dokumen' => $totalDocs,
                'total_views' => $totalViews,
                'total_downloads' => $totalDownloads,
                'dokumen_per_tahun' => $perYear,
                'dokumen_per_tipe' => $perType,
                'dokumen_per_status' => $perStatus,
                'most_viewed' => $mostViewed,
                'last_updated' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // ==============================================
    // 6. DOCUMENT TYPES
    // ==============================================
    public function documentTypes(Request $request)
    {
        // API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required'
            ], 401);
        }
        
        try {
            // Ambil dari data unik di tabel document
            $uniqueTypes = DB::table('document')
                ->select(DB::raw('DISTINCT jenis_peraturan as nama, singkatan_jenis as singkatan'))
                ->whereNotNull('jenis_peraturan')
                ->where('jenis_peraturan', '!=', '-')
                ->where('jenis_peraturan', '!=', '')
                ->orderBy('jenis_peraturan')
                ->get();
            
            // Beri ID
            $types = $uniqueTypes->map(function($item, $index) {
                return [
                    'id' => $index + 1,
                    'nama' => $item->nama ?? 'Unknown',
                    'singkatan' => $item->singkatan ?? ''
                ];
            });
            
            // Jika kosong, berikan default
            if ($types->isEmpty()) {
                $types = collect([
                    ['id' => 1, 'nama' => 'PERATURAN DAERAH', 'singkatan' => 'PERDA'],
                    ['id' => 2, 'nama' => 'PERATURAN WALIKOTA', 'singkatan' => 'PERWAL'],
                    ['id' => 3, 'nama' => 'KEPUTUSAN WALIKOTA', 'singkatan' => 'KEPWAL']
                ]);
            }
            
            // Return langsung array data tanpa wrapper 'success'
            return response()->json($types);
            
        } catch (\Exception $e) {
            // Return fallback data langsung tanpa wrapper
            return response()->json([
                ['id' => 1, 'nama' => 'PERATURAN DAERAH', 'singkatan' => 'PERDA'],
                ['id' => 2, 'nama' => 'PERATURAN WALIKOTA', 'singkatan' => 'PERWAL'],
                ['id' => 3, 'nama' => 'KEPUTUSAN WALIKOTA', 'singkatan' => 'KEPWAL']
            ]);
        }
    }
    
    // ==============================================
    // 7. GET ALL DOCUMENTS
    // ==============================================
    public function getAllDocuments(Request $request)
    {
        // API key check
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !str_starts_with($apiKey, 'jdih_')) {
            return response()->json([
                'success' => false,
                'message' => 'API key required'
            ], 401);
        }
        
        try {
            // Ambil semua dokumen
            $documents = DB::table('document')
                ->select([
                    'id',
                    'judul',
                    'nomor_peraturan',
                    'tahun_terbit',
                    'jenis_peraturan',
                    'singkatan_jenis',
                    'status',
                    'abstrak',
                    'bidang_hukum',
                    'created_at',
                    'updated_at'
                ])
                ->orderBy('tahun_terbit', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            
            // Format response
            $formattedDocs = $documents->map(function ($doc) {
                $fileInfo = $this->getDocumentFileInfo($doc->id);
                
                return [
                    'id' => $doc->id,
                    'judul' => $doc->judul ?? 'Tidak ada judul',
                    'nomor_peraturan' => $doc->nomor_peraturan ?? '-',
                    'tahun_terbit' => $doc->tahun_terbit ?? date('Y'),
                    'jenis' => $doc->jenis_peraturan ?? 'PERATURAN DAERAH',
                    'status' => $doc->status ?? 'Berlaku',
                    'bidang_hukum' => $doc->bidang_hukum ?? 'Umum',
                    'download_url' => $fileInfo['download_url'],
                    'has_file' => $fileInfo['has_file']
                ];
            });
            
            return response()->json([
                'total' => $documents->count(),
                'data' => $formattedDocs,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    // ==============================================
    // 8. JDIHN FORMAT ENDPOINTS
    // ==============================================
    
    /**
     * Search API dengan format JSON JDIHN - VERSI RESMI
     * GET /api/jdih/jdihn-format/search
     */
    public function jdihnSearch(Request $request)
    {
        try {
            // Parameters - ambil semua jika tidak ada limit
            $limit = (int) $request->get('limit', 0);
            
            // Query utama untuk format JDIHN
            $documentsQuery = DB::table('document')
                ->select([
                    'id',
                    'judul',
                    'nomor_peraturan',
                    'tahun_terbit',
                    'jenis_peraturan',
                    'singkatan_jenis',
                    'status',
                    'abstrak',
                    'bidang_hukum',
                    'tempat_terbit',
                    'tanggal_pengundangan',
                    'penerbit',
                    'bahasa',
                    'created_at'
                ]);
            
            // Filter pencarian
            if ($request->has('q') && !empty($request->q)) {
                $query = $request->q;
                $documentsQuery->where(function($q) use ($query) {
                    $q->where('judul', 'like', "%{$query}%")
                      ->orWhere('nomor_peraturan', 'like', "%{$query}%")
                      ->orWhere('abstrak', 'like', "%{$query}%");
                });
            }
            
            // Filter tahun
            if ($request->has('tahun')) {
                $documentsQuery->where('tahun_terbit', $request->tahun);
            }
            
            // Get results - jika limit > 0 gunakan limit, jika 0 ambil semua
            if ($limit > 0) {
                $documents = $documentsQuery->take($limit)->get();
            } else {
                $documents = $documentsQuery->get();
            }
            
            // Format response sesuai standar JDIHN PANDUAN
            $formattedDocs = $documents->map(function ($doc) {
                $fileInfo = $this->getDocumentFileInfoJDIHN($doc->id);
                
                // FORMAT RESMI SESUAI PANDUAN JDIHN
                return [
                    "idData" => (string) $doc->id,
                    "tahun_pengundangan" => (string) ($doc->tahun_terbit ?? date('Y')),
                    "tanggal_pengundangan" => $this->formatDate($doc->tanggal_pengundangan ?? ($doc->tahun_terbit . '-01-01')),
                    "jenis" => $doc->jenis_peraturan ?? 'PERATURAN DAERAH',
                    "noPeraturan" => $doc->nomor_peraturan ?? '-',
                    "judul" => $doc->judul ?? 'Tidak ada judul',
                    "noPanggil" => '',
                    "singkatanJenis" => $doc->singkatan_jenis ?? '',
                    "tempatTerbit" => $doc->tempat_terbit ?? 'KENDARI',
                    "penerbit" => $doc->penerbit ?? 'Pemerintah Kota Kendari',
                    "deskripsifisik" => 'PDF',
                    "sumber" => 'Lembaran Daerah Kota Kendari',
                    "subjek" => $doc->bidang_hukum ?? 'Umum',
                    "isbn" => '',
                    "status" => $this->formatStatus($doc->status ?? 'BERLAKU'),
                    "bahasa" => $doc->bahasa ?? 'Indonesia',
                    "lokasi" => 'Bagian Hukum Setda Kota Kendari',
                    "bidangHukum" => $doc->bidang_hukum ?? 'Umum',
                    "teuBadan" => 'Pemerintah Kota Kendari',
                    "nomorIndukBuku" => '',
                    "fileDownload" => $fileInfo['file_name'],
                    "urlDownload" => $fileInfo['download_url'],
                    "abstrak" => substr($doc->abstrak ?? '', 0, 200),
                    "urlAbstrak" => "https://jdih.kendarikota.go.id/api/jdih/documents/{$doc->id}",
                    "urlDetailPeraturan" => "https://jdih.kendarikota.go.id/dokumen/{$doc->id}",
                    "operasi" => "4",
                    "display" => "1"
                ];
            });
            
            // Return langsung array JSON sesuai panduan
            return response()->json($formattedDocs);
            
        } catch (\Exception $e) {
            // Untuk JDIHN, return array kosong jika error
            return response()->json([]);
        }
    }
    
    /**
     * Detail dokumen dengan format JSON JDIHN - VERSI RESMI
     * GET /api/jdih/jdihn-format/documents/{id}
     */
    public function jdihnShow($id, Request $request)
    {
        try {
            $document = DB::table('document')
                ->where('id', $id)
                ->first();
            
            if (!$document) {
                // Return object kosong jika tidak ditemukan
                return response()->json([]);
            }
            
            $fileInfo = $this->getDocumentFileInfoJDIHN($id);
            
            // FORMAT RESMI SESUAI PANDUAN JDIHN
            $formattedDoc = [
                "idData" => (string) $document->id,
                "tahun_pengundangan" => (string) ($document->tahun_terbit ?? date('Y')),
                "tanggal_pengundangan" => $this->formatDate($document->tanggal_pengundangan ?? ($document->tahun_terbit . '-01-01')),
                "jenis" => $document->jenis_peraturan ?? 'PERATURAN DAERAH',
                "noPeraturan" => $document->nomor_peraturan ?? '-',
                "judul" => $document->judul ?? 'Tidak ada judul',
                "noPanggil" => '',
                "singkatanJenis" => $document->singkatan_jenis ?? '',
                "tempatTerbit" => $document->tempat_terbit ?? 'KENDARI',
                "penerbit" => $document->penerbit ?? 'Pemerintah Kota Kendari',
                "deskripsifisik" => 'PDF',
                "sumber" => 'Lembaran Daerah Kota Kendari',
                "subjek" => $document->bidang_hukum ?? 'Umum',
                "isbn" => '',
                "status" => $this->formatStatus($document->status ?? 'BERLAKU'),
                "bahasa" => $document->bahasa ?? 'Indonesia',
                "lokasi" => 'Bagian Hukum Setda Kota Kendari',
                "bidangHukum" => $document->bidang_hukum ?? 'Umum',
                "teuBadan" => 'Pemerintah Kota Kendari',
                "nomorIndukBuku" => '',
                "fileDownload" => $fileInfo['file_name'],
                "urlDownload" => $fileInfo['download_url'],
                "abstrak" => $document->abstrak ?? '',
                "urlAbstrak" => "https://jdih.kendarikota.go.id/api/jdih/documents/{$document->id}",
                "urlDetailPeraturan" => "https://jdih.kendarikota.go.id/dokumen/{$document->id}",
                "operasi" => "4",
                "display" => "1"
            ];
            
            // Return langsung object JSON sesuai panduan
            return response()->json($formattedDoc);
            
        } catch (\Exception $e) {
            // Return object kosong jika error
            return response()->json([]);
        }
    }
    
    /**
     * Endpoint khusus untuk sinkronisasi JDIHN (semua data)
     * GET /api/jdih/jdihn-format/all
     */
    public function jdihnAll(Request $request)
    {
        try {
            // Ambil semua dokumen tanpa filter
            $documents = DB::table('document')
                ->select([
                    'id',
                    'judul',
                    'nomor_peraturan',
                    'tahun_terbit',
                    'jenis_peraturan',
                    'singkatan_jenis',
                    'status',
                    'abstrak',
                    'bidang_hukum',
                    'tempat_terbit',
                    'tanggal_pengundangan',
                    'penerbit',
                    'bahasa'
                ])
                ->orderBy('tahun_terbit', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            
            // Format response sesuai standar JDIHN PANDUAN
            $formattedDocs = $documents->map(function ($doc) {
                $fileInfo = $this->getDocumentFileInfoJDIHN($doc->id);
                
                return [
                    "idData" => (string) $doc->id,
                    "tahun_pengundangan" => (string) ($doc->tahun_terbit ?? date('Y')),
                    "tanggal_pengundangan" => $this->formatDate($doc->tanggal_pengundangan ?? ($doc->tahun_terbit . '-01-01')),
                    "jenis" => $doc->jenis_peraturan ?? 'PERATURAN DAERAH',
                    "noPeraturan" => $doc->nomor_peraturan ?? '-',
                    "judul" => $doc->judul ?? 'Tidak ada judul',
                    "noPanggil" => '',
                    "singkatanJenis" => $doc->singkatan_jenis ?? '',
                    "tempatTerbit" => $doc->tempat_terbit ?? 'KENDARI',
                    "penerbit" => $doc->penerbit ?? 'Pemerintah Kota Kendari',
                    "deskripsifisik" => 'PDF',
                    "sumber" => 'Lembaran Daerah Kota Kendari',
                    "subjek" => $doc->bidang_hukum ?? 'Umum',
                    "isbn" => '',
                    "status" => $this->formatStatus($doc->status ?? 'BERLAKU'),
                    "bahasa" => $doc->bahasa ?? 'Indonesia',
                    "lokasi" => 'Bagian Hukum Setda Kota Kendari',
                    "bidangHukum" => $doc->bidang_hukum ?? 'Umum',
                    "teuBadan" => 'Pemerintah Kota Kendari',
                    "nomorIndukBuku" => '',
                    "fileDownload" => $fileInfo['file_name'],
                    "urlDownload" => $fileInfo['download_url'],
                    "abstrak" => substr($doc->abstrak ?? '', 0, 200),
                    "urlAbstrak" => "https://jdih.kendarikota.go.id/api/jdih/documents/{$doc->id}",
                    "urlDetailPeraturan" => "https://jdih.kendarikota.go.id/dokumen/{$doc->id}",
                    "operasi" => "4",
                    "display" => "1"
                ];
            });
            
            // Return langsung array JSON
            return response()->json($formattedDocs);
            
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
    
    // ==============================================
    // HELPER METHODS
    // ==============================================
    
    /**
     * Truncate text
     */
    private function truncateText($text, $length = 200)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        return $text . '...';
    }
    
    /**
     * Get document file info - CORRECT VERSION
     */
    private function getDocumentFileInfo($documentId)
    {
        try {
            // Cari file di tabel data_lampiran
            $file = DB::table('data_lampiran')
                ->where('id_dokumen', $documentId)
                ->where(function($query) {
                    $query->whereNotNull('dokumen_lampiran')
                          ->orWhereNotNull('url_lampiran');
                })
                ->orderBy('id', 'desc')
                ->first();
            
            if ($file) {
                $fileUrl = $file->url_lampiran ?? $file->dokumen_lampiran;
                $fileName = $file->judul_lampiran ?? ('document_' . $documentId . '.pdf');
                
                // Cek jika URL lengkap
                $isFullUrl = false;
                $downloadUrl = $fileUrl;
                
                if ($fileUrl && filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                    $isFullUrl = true;
                    $downloadUrl = $fileUrl;
                } elseif ($fileUrl && str_starts_with($fileUrl, 'http')) {
                    $isFullUrl = true;
                    $downloadUrl = $fileUrl;
                } else {
                    // Jika bukan URL lengkap, coba buat URL relatif
                    $downloadUrl = $fileUrl ? url('storage/' . $fileUrl) : null;
                }
                
                return [
                    'file_name' => $fileName,
                    'file_url' => $fileUrl,
                    'download_url' => $downloadUrl,
                    'is_full_url' => $isFullUrl,
                    'has_file' => true,
                    'file_type' => pathinfo($fileUrl, PATHINFO_EXTENSION) ?? 'pdf'
                ];
            }
            
        } catch (\Exception $e) {
            // Jika error, return info default
        }
        
        // Default jika tidak ada file
        return [
            'file_name' => 'document_' . $documentId . '.pdf',
            'download_url' => url("/dokumen/download/{$documentId}"),
            'has_file' => false,
            'note' => 'File information not available in database'
        ];
    }
    
    /**
     * Get document file info khusus untuk format JDIHN
     */
    private function getDocumentFileInfoJDIHN($documentId)
    {
        try {
            // Cari file di tabel data_lampiran
            $file = DB::table('data_lampiran')
                ->where('id_dokumen', $documentId)
                ->where(function($query) {
                    $query->whereNotNull('dokumen_lampiran')
                          ->orWhereNotNull('url_lampiran');
                })
                ->orderBy('id', 'desc')
                ->first();
            
            if ($file) {
                $fileUrl = $file->url_lampiran ?? $file->dokumen_lampiran;
                $fileName = $file->judul_lampiran ?? ('document_' . $documentId . '.pdf');
                
                // Format download URL
                if ($fileUrl && filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                    $downloadUrl = $fileUrl;
                } elseif ($fileUrl && str_starts_with($fileUrl, 'http')) {
                    $downloadUrl = $fileUrl;
                } else {
                    $downloadUrl = $fileUrl ? url('storage/' . $fileUrl) : 
                        url("/dokumen/download/{$documentId}");
                }
                
                return [
                    'file_name' => $fileName,
                    'download_url' => $downloadUrl
                ];
            }
            
        } catch (\Exception $e) {
            // Fallback jika error
        }
        
        // Default jika tidak ada file
        return [
            'file_name' => 'document_' . $documentId . '.pdf',
            'download_url' => url("/dokumen/download/{$documentId}")
        ];
    }
    
    /**
     * Format tanggal untuk JDIHN
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return date('Y') . '-01-01';
        }
        
        try {
            // Coba parse berbagai format tanggal
            if (strtotime($date) !== false) {
                return date('Y-m-d', strtotime($date));
            }
        } catch (\Exception $e) {
            // Jika error, return default
        }
        
        return date('Y-m-d');
    }
    
    /**
     * Format status untuk JDIHN
     */
    private function formatStatus($status)
    {
        $status = strtoupper(trim($status));
        
        // Mapping status ke format JDIHN
        $statusMap = [
            'BERLAKU' => 'BERLAKU',
            'TIDAK BERLAKU' => 'TIDAK BERLAKU',
            'DICABUT' => 'TIDAK BERLAKU',
            'PERUBAHAN' => 'BERLAKU',
            'MASIH BERLAKU' => 'BERLAKU',
            'BELUM BERLAKU' => 'TIDAK BERLAKU'
        ];
        
        return $statusMap[$status] ?? 'BERLAKU';
    }
}
