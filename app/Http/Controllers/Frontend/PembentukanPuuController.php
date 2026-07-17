<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PembentukanPuu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PembentukanPuuController extends Controller
{
    // Data kategori - DIPERBAIKI: Hanya untuk frontend
    private $categories = [
        ['value' => 'naskah-akademik', 'label' => 'Naskah Akademik'],
        ['value' => 'rancangan-puu', 'label' => 'Rancangan PUU'],
        ['value' => 'penelitian-hukum', 'label' => 'Penelitian Hukum'],
        ['value' => 'pengkajian-hukum', 'label' => 'Pengkajian Hukum'],
        ['value' => 'pengkajian-konstitusi', 'label' => 'Pengkajian Konstitusi'],
        ['value' => 'analisis-evaluasi', 'label' => 'Analisis & Evaluasi'],
    ];
    
    // Mapping kategori frontend ke database
    private $categoryMapping = [
        'naskah-akademik' => 'naskah_akademik',
        'rancangan-puu' => 'rancangan_puu',
        'penelitian-hukum' => 'penelitian_hukum',
        'pengkajian-hukum' => 'pengkajian_hukum',
        'pengkajian-konstitusi' => 'pengkajian_konstitusi',
        'analisis-evaluasi' => 'analisis_evaluasi',
    ];
    
    // Deskripsi kategori
    private $categoryDescriptions = [
        'naskah-akademik' => 'Dokumen akademis sebagai dasar penyusunan peraturan',
        'rancangan-puu' => 'Rancangan peraturan yang sedang dalam proses pembahasan',
        'penelitian-hukum' => 'Hasil penelitian mendalam tentang aspek hukum',
        'pengkajian-hukum' => 'Kajian mendalam terhadap aspek hukum tertentu',
        'pengkajian-konstitusi' => 'Analisis kesesuaian dengan konstitusi dan UUD',
        'analisis-evaluasi' => 'Evaluasi implementasi dan dampak peraturan',
    ];

    // Helper: Get category by value
    private function getCategoryByValue($value)
    {
        foreach ($this->categories as $category) {
            if ($category['value'] === $value) {
                return $category;
            }
        }
        return null;
    }

    // Halaman utama Pembentukan PUU - DIPERBAIKI
    public function index(Request $request)
    {
        // Kategori yang dipilih (default: naskah-akademik)
        $selectedCategoryValue = 'naskah-akademik';
        $searchQuery = $request->get('q', '');
        
        // Ambil data kategori lengkap
        $selectedCategory = $this->getCategoryByValue($selectedCategoryValue);
        
        if (!$selectedCategory) {
            abort(404, 'Kategori default tidak ditemukan');
        }
        
        // Ambil semua dokumen untuk statistik
        $allDocuments = $this->getAllDocumentsCount();
        
        // Ambil dokumen untuk kategori default
        $documents = $this->getDocumentsByCategory($selectedCategoryValue, $request);
        
        // Log untuk debugging
        \Log::info('Loading Pembentukan PUU index page', [
            'category' => $selectedCategoryValue,
            'search_query' => $searchQuery,
            'documents_count' => count($documents['data'])
        ]);
        
        return view('frontend.pembentukan-PUU.index', [
            'categories' => $this->categories,
            'categoryDescriptions' => $this->categoryDescriptions,
            'selectedCategory' => $selectedCategory,
            'allDocuments' => $allDocuments,
            'documents' => $documents['data'],
            'pagination' => $documents['pagination'],
            'searchQuery' => $searchQuery,
            'filter_tahun' => $request->get('filter_tahun', ''),
            'filter_nomor' => $request->get('filter_nomor', ''),
        ]);
    }
    
    // Halaman berdasarkan kategori - DIPERBAIKI untuk route /pembentukan-puu/kategori/{kategori}
    public function kategori($kategori, Request $request)
    {
        // Validasi kategori
        if (!array_key_exists($kategori, $this->categoryMapping)) {
            abort(404, 'Kategori tidak ditemukan');
        }
        
        // Ambil data kategori lengkap
        $selectedCategory = $this->getCategoryByValue($kategori);
        if (!$selectedCategory) {
            abort(404, 'Data kategori tidak ditemukan');
        }
        
        $searchQuery = $request->get('q', '');
        
        // Ambil semua dokumen untuk statistik
        $allDocuments = $this->getAllDocumentsCount();
        
        // Ambil dokumen untuk kategori yang dipilih
        $documents = $this->getDocumentsByCategory($kategori, $request);
        
        // Debug: Log untuk troubleshooting
        \Log::info('Loading category page', [
            'category' => $kategori,
            'selectedCategory' => $selectedCategory,
            'documents_count' => count($documents['data']),
            'search_query' => $searchQuery
        ]);
        
        return view('frontend.pembentukan-PUU.index', [
            'categories' => $this->categories,
            'categoryDescriptions' => $this->categoryDescriptions,
            'selectedCategory' => $selectedCategory,
            'allDocuments' => $allDocuments,
            'documents' => $documents['data'],
            'pagination' => $documents['pagination'],
            'searchQuery' => $searchQuery,
            'filter_tahun' => $request->get('filter_tahun', ''),
            'filter_nomor' => $request->get('filter_nomor', ''),
        ]);
    }
    
    // Detail dokumen
    public function show($id)
    {
        $document = PembentukanPuu::where('id', $id)
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->first();
        
        if (!$document) {
            abort(404, 'Dokumen tidak ditemukan atau tidak tersedia untuk umum');
        }
        
        // Increment views
        $document->increment('views');
        
        // Format kategori untuk tampilan
        $kategoriLabel = '';
        $kategoriValue = '';
        
        // Cari kategori berdasarkan mapping database ke frontend
        $dbValue = $document->jenis_dokumen;
        $kategoriValue = array_search($dbValue, $this->categoryMapping);
        
        if ($kategoriValue) {
            $category = $this->getCategoryByValue($kategoriValue);
            $kategoriLabel = $category ? $category['label'] : 'Dokumen';
        } else {
            $kategoriLabel = 'Dokumen';
            $kategoriValue = 'naskah-akademik';
        }
        
        // Dokumen terkait
        $relatedDocuments = PembentukanPuu::where('jenis_dokumen', $document->jenis_dokumen)
            ->where('id', '!=', $document->id)
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        return view('frontend.pembentukan-PUU.show', [
            'document' => $document,
            'kategoriLabel' => $kategoriLabel,
            'kategoriValue' => $kategoriValue,
            'relatedDocuments' => $relatedDocuments,
        ]);
    }
    
    // Download file
    public function download($id, $type)
    {
        $document = PembentukanPuu::where('id', $id)
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->first();
        
        if (!$document) {
            abort(404, 'Dokumen tidak ditemukan atau tidak tersedia untuk umum');
        }
        
        $filePath = '';
        $fileName = '';
        
        switch ($type) {
            case 'dokumen':
                $filePath = $document->dokumen_utama;
                $fileName = 'dokumen_' . Str::slug($document->judul) . '.pdf';
                break;
            case 'cover':
                $filePath = $document->cover;
                $fileName = 'cover_' . Str::slug($document->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            case 'lampiran':
                $filePath = $document->lampiran;
                $fileName = 'lampiran_' . Str::slug($document->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            default:
                abort(404, 'Tipe file tidak valid');
        }
        
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }
        
        // Update download counter
        $document->increment('jumlah_download');
        
        return \Illuminate\Support\Facades\Storage::disk('public')->download($filePath, $fileName);
    }

    // Helper: Get all documents count for statistics
    private function getAllDocumentsCount()
    {
        $allDocuments = [];
        
        foreach ($this->categories as $category) {
            $dbValue = $this->categoryMapping[$category['value']] ?? null;
            if (!$dbValue) continue;
            
            $count = PembentukanPuu::where('jenis_dokumen', $dbValue)
                ->where('status_publikasi', 'published')
                ->where('hak_akses', 'public')
                ->count();
            
            $allDocuments[$category['value']] = $count;
        }
        
        return $allDocuments;
    }
    
    // Helper: Get documents by category - DIPERBAIKI dengan filter lengkap
    private function getDocumentsByCategory($category, Request $request)
    {
        // Cek mapping kategori
        if (!array_key_exists($category, $this->categoryMapping)) {
            return [
                'data' => [],
                'pagination' => null
            ];
        }
        
        $dbValue = $this->categoryMapping[$category];
        
        // Query documents
        $query = PembentukanPuu::where('jenis_dokumen', $dbValue)
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public');
        
        // Search functionality
        $searchQuery = $request->get('q', '');
        if (!empty($searchQuery)) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('judul', 'like', '%' . $searchQuery . '%')
                  ->orWhere('abstrak', 'like', '%' . $searchQuery . '%')
                  ->orWhere('kata_kunci', 'like', '%' . $searchQuery . '%')
                  ->orWhere('penulis', 'like', '%' . $searchQuery . '%')
                  ->orWhere('lembaga_pemrakarsa', 'like', '%' . $searchQuery . '%')
                  ->orWhere('nomor_dokumen', 'like', '%' . $searchQuery . '%');
            });
        }
        
        // Filter tahun
        $filterTahun = $request->get('filter_tahun', '');
        if (!empty($filterTahun)) {
            $query->where('tahun', $filterTahun);
        }
        
        // Filter nomor dokumen
        $filterNomor = $request->get('filter_nomor', '');
        if (!empty($filterNomor)) {
            $query->where('nomor_dokumen', 'like', '%' . $filterNomor . '%');
        }
        
        // Filter kategori (untuk form filter modal)
        $filterKategori = $request->get('filter_kategori', '');
        if (!empty($filterKategori) && $filterKategori !== $category) {
            // Jika filter kategori berbeda, redirect ke kategori yang dipilih
            return $this->redirectToCategory($filterKategori, $request);
        }
        
        // Order dan pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        
        $totalDocuments = $query->count();
        $documentsPaginated = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $currentPage);
        
        // Format data untuk view
        $formattedData = $documentsPaginated->map(function($document) use ($category) {
            return $this->formatDocumentForView($document, $category);
        });
        
        return [
            'data' => $formattedData,
            'pagination' => $documentsPaginated,
            'total' => $totalDocuments
        ];
    }
    
    // Helper: Redirect to different category
    private function redirectToCategory($category, Request $request)
    {
        $params = $request->except(['filter_kategori', 'page']);
        $params['filter_kategori'] = $category;
        
        return redirect()->route('frontend.pembentukan-puu.kategori', array_merge(['kategori' => $category], $params));
    }
    
    // Helper: Format document data for view - DIPERBAIKI
    private function formatDocumentForView($document, $category)
    {
        // Format tanggal unggah
        $tanggalUnggah = '-';
        if ($document->tanggal_unggah) {
            try {
                $tanggalUnggah = Carbon::parse($document->tanggal_unggah)->format('d/m/Y');
            } catch (\Exception $e) {
                $tanggalUnggah = $document->tanggal_unggah;
            }
        }
        
        // Format created_at sebagai fallback
        $createdAt = $document->created_at ? $document->created_at->format('d/m/Y') : '-';
        
        // Buat description dari abstrak atau judul
        $description = '';
        if (!empty($document->abstrak)) {
            $description = Str::limit(strip_tags($document->abstrak), 150);
        } elseif (!empty($document->keterangan)) {
            $description = Str::limit(strip_tags($document->keterangan), 150);
        } else {
            $description = 'Dokumen ' . Str::limit($document->judul, 100);
        }
        
        // Cek apakah ada file PDF
        $hasPdf = !empty($document->dokumen_utama) && 
                  \Illuminate\Support\Facades\Storage::disk('public')->exists($document->dokumen_utama);
        
        return [
            'id' => $document->id,
            'title' => $document->judul,
            'description' => $description,
            'year' => $document->tahun,
            'author' => $document->penulis,
            'uploader' => $document->pengunggah,
            'pages' => $document->jumlah_halaman,
            'institution' => $document->lembaga_pemrakarsa,
            'category' => $category,
            'download_url' => $hasPdf ? route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'dokumen']) : '#',
            'detail_url' => route('frontend.pembentukan-puu.show', $document->id),
            'created_at' => $createdAt,
            'tanggal_unggah' => $tanggalUnggah,
            'upload_date' => $tanggalUnggah,
            'abstract' => $document->abstrak,
            'keywords' => $document->kata_kunci,
            'status_dokumen' => $document->status_dokumen,
            'has_pdf' => $hasPdf,
            'has_cover' => !empty($document->cover) && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->cover),
            'has_attachment' => !empty($document->lampiran) && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->lampiran),
            'downloads' => $document->jumlah_download,
            'views' => $document->views,
            'nomor_dokumen' => $document->nomor_dokumen,
        ];
    }
    
    // API untuk mendapatkan abstract dokumen (untuk modal)
    public function getAbstract($id)
    {
        $document = PembentukanPuu::where('id', $id)
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->first();
        
        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'abstract' => $document->abstrak,
            'title' => $document->judul,
            'year' => $document->tahun,
            'author' => $document->penulis
        ]);
    }
    
    // Halaman statistik (opsional)
    public function statistik()
    {
        // Ambil statistik dokumen per kategori
        $stats = [];
        $totalDocuments = 0;
        $totalDownloads = 0;
        $totalViews = 0;
        
        foreach ($this->categories as $category) {
            $dbValue = $this->categoryMapping[$category['value']] ?? null;
            if (!$dbValue) continue;
            
            $documents = PembentukanPuu::where('jenis_dokumen', $dbValue)
                ->where('status_publikasi', 'published')
                ->where('hak_akses', 'public')
                ->get();
            
            $count = $documents->count();
            $downloads = $documents->sum('jumlah_download');
            $views = $documents->sum('views');
            
            $stats[] = [
                'label' => $category['label'],
                'value' => $category['value'],
                'count' => $count,
                'downloads' => $downloads,
                'views' => $views,
                'percentage' => 0 // Akan diisi nanti
            ];
            
            $totalDocuments += $count;
            $totalDownloads += $downloads;
            $totalViews += $views;
        }
        
        // Hitung persentase
        foreach ($stats as &$stat) {
            $stat['percentage'] = $totalDocuments > 0 ? round(($stat['count'] / $totalDocuments) * 100, 1) : 0;
        }
        
        // Data untuk chart
        $chartData = [
            'labels' => array_column($stats, 'label'),
            'counts' => array_column($stats, 'count'),
            'colors' => ['#4f46e5', '#8b5cf6', '#6366f1', '#a855f7', '#d946ef', '#ec4899']
        ];
        
        return view('frontend.pembentukan-PUU.statistik', [
            'stats' => $stats,
            'totalDocuments' => $totalDocuments,
            'totalDownloads' => $totalDownloads,
            'totalViews' => $totalViews,
            'chartData' => $chartData,
            'categories' => $this->categories,
        ]);
    }
    
    // Halaman tentang (opsional)
    public function tentang()
    {
        return view('frontend.pembentukan-PUU.tentang', [
            'categories' => $this->categories,
            'categoryDescriptions' => $this->categoryDescriptions
        ]);
    }
}
