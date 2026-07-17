<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Disabilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Tambahkan ini

class DisabilitasController extends Controller
{
    public function index(Request $request)
    {
        $query = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->orderBy('created_at', 'desc');
        
        // Filter pencarian
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('abstrak', 'like', "%{$search}%")
                  ->orWhere('kata_kunci', 'like', "%{$search}%")
                  ->orWhere('nomor_dokumen', 'like', "%{$search}%");
            });
        }
        
        // Filter jenis dokumen
        if ($request->has('jenis') && $request->jenis) {
            $query->where('jenis_dokumen', $request->jenis);
        }
        
        // Filter tahun
        if ($request->has('tahun') && $request->tahun) {
            $query->where('tahun', $request->tahun);
        }
        
        $disabilitas = $query->paginate(12);
        
        // Stats untuk AI search
        $totalDocuments = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->count();
        
        $years = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->distinct('tahun')
            ->pluck('tahun')
            ->sortDesc();
        
        return view('frontend.disabilitas.index', compact(
            'disabilitas', 
            'totalDocuments',
            'years'
        ));
    }
    
    public function show($id)
    {
        $disabilitas = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->findOrFail($id);
            
        $relatedDocuments = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->where('id', '!=', $id)
            ->where(function($query) use ($disabilitas) {
                $query->where('jenis_dokumen', $disabilitas->jenis_dokumen)
                    ->orWhere('ruang_lingkup', $disabilitas->ruang_lingkup)
                    ->orWhere('tahun', $disabilitas->tahun);
            })
            ->limit(3)
            ->get();
            
        return view('frontend.disabilitas.show', compact('disabilitas', 'relatedDocuments'));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        // Pencarian AI (simulasi)
        if (strpos(strtolower($query), 'tentang') !== false || 
            strpos(strtolower($query), 'apa') !== false ||
            strpos(strtolower($query), 'bagaimana') !== false) {
            
            $results = $this->getAiSearchResults($query);
            return response()->json([
                'type' => 'ai',
                'query' => $query,
                'results' => $results
            ]);
        }
        
        // Pencarian reguler
        $documents = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('abstrak', 'like', "%{$query}%")
                  ->orWhere('kata_kunci', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->judul,
                    'type' => $doc->jenis_dokumen_formatted,
                    'year' => $doc->tahun,
                    'abstract' => substr($doc->abstrak, 0, 150) . '...',
                    'url' => route('frontend.disabilitas.show', $doc->id),
                    'download_url' => route('frontend.disabilitas.download', ['id' => $doc->id, 'type' => 'dokumen'])
                ];
            });
            
        return response()->json([
            'type' => 'regular',
            'query' => $query,
            'count' => $documents->count(),
            'results' => $documents
        ]);
    }
    
    public function getAbstract($id)
    {
        $disabilitas = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'abstract' => $disabilitas->abstrak
        ]);
    }
    
    private function getAiSearchResults($query)
    {
        // Simulasi AI search dengan analisis data real
        $keywords = $this->extractKeywords($query);
        
        $analysis = "Berdasarkan database dokumen disabilitas Kota Kendari, ";
        
        // Hitung dokumen terkait
        $relatedCount = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('judul', 'like', "%{$keyword}%")
                      ->orWhere('abstrak', 'like', "%{$keyword}%")
                      ->orWhere('kata_kunci', 'like', "%{$keyword}%");
                }
            })
            ->count();
            
        $analysis .= "terdapat {$relatedCount} dokumen yang relevan dengan '{$query}'. ";
        
        // Dapatkan top dokumen
        $topDocuments = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('judul', 'like', "%{$keyword}%")
                      ->orWhere('abstrak', 'like', "%{$keyword}%");
                }
            })
            ->limit(3)
            ->get()
            ->map(function($doc) {
                return [
                    'title' => $doc->judul,
                    'type' => $doc->jenis_dokumen_formatted,
                    'year' => $doc->tahun,
                    'id' => $doc->id,
                    'url' => route('frontend.disabilitas.show', $doc->id),
                    'download_url' => route('frontend.disabilitas.download', ['id' => $doc->id, 'type' => 'dokumen'])
                ];
            });
            
        $analysis .= "Beberapa dokumen utama meliputi: ";
        
        foreach ($topDocuments as $index => $doc) {
            $analysis .= "{$doc['title']} ({$doc['type']} {$doc['year']}). ";
        }
        
        // Tambahkan rekomendasi
        $analysis .= "Untuk informasi lebih detail, silakan klik dokumen terkait.";
        
        return [
            'analysis' => $analysis,
            'top_documents' => $topDocuments,
            'total_related' => $relatedCount,
            'keywords' => $keywords
        ];
    }
    
    private function extractKeywords($query)
    {
        $commonWords = ['tentang', 'apa', 'bagaimana', 'dimana', 'kapan', 'siapa', 'adalah', 'dengan', 'untuk', 'pada'];
        $words = explode(' ', strtolower($query));
        
        return array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords);
        });
    }
    
    public function filter(Request $request)
    {
        $query = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public');
            
        if ($request->has('jenis_dokumen') && $request->jenis_dokumen) {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }
        
        if ($request->has('tahun') && $request->tahun) {
            $query->where('tahun', $request->tahun);
        }
        
        if ($request->has('ruang_lingkup') && $request->ruang_lingkup) {
            $query->where('ruang_lingkup', $request->ruang_lingkup);
        }
        
        $documents = $query->orderBy('created_at', 'desc')->get();
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $documents->count(),
                'documents' => $documents->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'title' => $doc->judul,
                        'type' => $doc->jenis_dokumen_formatted,
                        'year' => $doc->tahun,
                        'abstract' => substr($doc->abstrak, 0, 100) . '...',
                        'url' => route('frontend.disabilitas.show', $doc->id),
                        'download_url' => route('frontend.disabilitas.download', ['id' => $doc->id, 'type' => 'dokumen'])
                    ];
                })
            ]);
        }
        
        return view('frontend.disabilitas.index', compact('documents'));
    }
    
    /**
     * Method untuk download file berdasarkan tipe
     * @param int $id
     * @param string $type Jenis file: dokumen, cover, lampiran
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id, $type = 'dokumen')
    {
        $disabilitas = Disabilitas::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->findOrFail($id);
            
        $filePath = '';
        $fileName = '';
        
        switch ($type) {
            case 'dokumen':
                if (!$disabilitas->dokumen_utama) {
                    abort(404, 'File dokumen tidak ditemukan');
                }
                $filePath = $disabilitas->dokumen_utama;
                $fileName = 'dokumen_' . Str::slug($disabilitas->judul) . '.pdf'; // Perubahan di sini
                break;
                
            case 'cover':
                if (!$disabilitas->cover) {
                    abort(404, 'File cover tidak ditemukan');
                }
                $filePath = $disabilitas->cover;
                $fileName = 'cover_' . Str::slug($disabilitas->judul) . '.' . // Perubahan di sini
                           pathinfo($filePath, PATHINFO_EXTENSION);
                break;
                
            case 'lampiran':
                if (!$disabilitas->lampiran) {
                    abort(404, 'File lampiran tidak ditemukan');
                }
                $filePath = $disabilitas->lampiran;
                $fileName = 'lampiran_' . Str::slug($disabilitas->judul) . '.' . // Perubahan di sini
                           pathinfo($filePath, PATHINFO_EXTENSION);
                break;
                
            default:
                abort(404, 'Tipe file tidak valid. Gunakan: dokumen, cover, atau lampiran');
        }
        
        // Cek apakah file ada di storage
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di server');
        }
        
        // Return file untuk di-download
        return Storage::disk('public')->download($filePath, $fileName);
    }
}