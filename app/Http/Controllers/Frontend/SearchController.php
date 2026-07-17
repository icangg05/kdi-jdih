<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\LayananDisabilitas;
use App\Models\PembentukanPuu;
use App\Models\Pengumuman;
use App\Models\InformasiHukum;
use App\Models\Berita;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Pencarian global - Mengumpulkan hasil dari semua sumber
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->back()->with('error', 'Silakan masukkan kata kunci pencarian');
        }
        
        $results = collect();
        
        // 1. Cari di DOKUMEN (Livewire component)
        $dokumenResults = $this->searchDokumen($query);
        $results = $results->merge($dokumenResults);
        
        // 2. Cari di PENGUMUMAN (Livewire component)
        $pengumumanResults = $this->searchPengumuman($query);
        $results = $results->merge($pengumumanResults);
        
        // 3. Cari di INFORMASI HUKUM (Livewire component)
        $informasiHukumResults = $this->searchInformasiHukum($query);
        $results = $results->merge($informasiHukumResults);
        
        // 4. Cari di BERITA (Livewire component)
        $beritaResults = $this->searchBerita($query);
        $results = $results->merge($beritaResults);
        
        // 5. Cari di LAYANAN DISABILITAS (jika model ada)
        $disabilitasResults = $this->searchDisabilitas($query);
        $results = $results->merge($disabilitasResults);
        
        // 6. Cari di PEMBENTUKAN PUU (jika model ada)
        $puuResults = $this->searchPembentukanPuu($query);
        $results = $results->merge($puuResults);
        
        // Urutkan berdasarkan relevansi
        $results = $this->sortResultsByRelevance($results, $query);
        
        return view('frontend.search.global', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $results->count(),
            'categories' => $this->getCategoryCounts($results)
        ]);
    }
    
    /**
     * Redirect pencarian berdasarkan kata kunci (Smart Search)
     */
    public function smartRedirect(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('frontend.beranda');
        }
        
        $lowerQuery = strtolower($query);
        
        // Deteksi kata kunci untuk LAYANAN DISABILITAS
        $disabilitasKeywords = ['disabilitas', 'difabel', 'cacat', 'aksesibilitas', 'inklusi', 'penyandang', 'tunanetra', 'tunadaksa'];
        foreach ($disabilitasKeywords as $keyword) {
            if (strpos($lowerQuery, $keyword) !== false) {
                return redirect()->route('frontend.layanan-disabilitas.index', ['q' => $query]);
            }
        }
        
        // Deteksi kata kunci untuk PEMBENTUKAN/PERANCANGAN PUU
        $puuKeywords = ['puu', 'perancangan puu', 'pembentukan undang-undang', 'legislasi', 'rancangan', 'naskah akademik', 'undang-undang', 'uu'];
        foreach ($puuKeywords as $keyword) {
            if (strpos($lowerQuery, $keyword) !== false) {
                return redirect()->route('frontend.pembentukan-puu.index', ['q' => $query]);
            }
        }
        
        // Deteksi kategori DOKUMEN berdasarkan kata kunci
        $documentKeywords = [
            'peraturan' => ['peraturan', 'perda', 'perwali', 'pergub', 'permen', 'pp', 'undang'],
            'monografi' => ['monografi', 'buku', 'kajian', 'studi', 'penelitian'],
            'artikel' => ['artikel', 'jurnal', 'makalah', 'paper'],
            'putusan' => ['putusan', 'vonis', 'keputusan', 'penetapan'],
            'pengumuman' => ['pengumuman', 'pemberitahuan', 'announcement', 'notifikasi'],
            'informasi hukum' => ['informasi hukum', 'legal information', 'hukum', 'yurisprudensi']
        ];
        
        foreach ($documentKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($lowerQuery, $keyword) !== false) {
                    return redirect()->route('frontend.dokumen.index', ['kategori' => $this->getKategoriFromCategory($category), 'q' => $query]);
                }
            }
        }
        
        // Default ke pencarian global
        return redirect()->route('frontend.search.global', ['q' => $query]);
    }
    
    /**
     * Helper: Search in Dokumen
     */
    private function searchDokumen($query)
    {
        if (!class_exists('App\Models\Dokumen')) {
            return collect();
        }
        
        try {
            $results = Dokumen::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                ->orWhere('isi', 'LIKE', "%{$query}%")
                ->orWhere('nomor', 'LIKE', "%{$query}%")
                ->orWhere('tahun', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'dokumen';
                $item->search_category = $item->kategori ?? 'dokumen';
                $item->route = route('frontend.dokumen.show', ['kategori' => $item->kategori, 'id' => $item->id]);
                $item->excerpt = Str::limit($item->deskripsi ?? $item->isi, 150);
                $item->icon = $this->getIconForCategory($item->kategori);
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Search in Pengumuman
     */
    private function searchPengumuman($query)
    {
        if (!class_exists('App\Models\Pengumuman')) {
            return collect();
        }
        
        try {
            $results = Pengumuman::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('isi', 'LIKE', "%{$query}%")
                ->orWhere('ringkasan', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'pengumuman';
                $item->search_category = 'pengumuman';
                $item->route = route('frontend.pengumuman.show', $item->id);
                $item->excerpt = Str::limit($item->ringkasan ?? $item->isi, 150);
                $item->icon = 'fas fa-bullhorn';
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Search in Informasi Hukum
     */
    private function searchInformasiHukum($query)
    {
        if (!class_exists('App\Models\InformasiHukum')) {
            return collect();
        }
        
        try {
            $results = InformasiHukum::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('isi', 'LIKE', "%{$query}%")
                ->orWhere('ringkasan', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'informasi_hukum';
                $item->search_category = 'informasi hukum';
                $item->route = route('frontend.informasi-hukum.show', $item->id);
                $item->excerpt = Str::limit($item->ringkasan ?? $item->isi, 150);
                $item->icon = 'fas fa-gavel';
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Search in Berita
     */
    private function searchBerita($query)
    {
        if (!class_exists('App\Models\Berita')) {
            return collect();
        }
        
        try {
            $results = Berita::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('isi', 'LIKE', "%{$query}%")
                ->orWhere('ringkasan', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'berita';
                $item->search_category = 'berita';
                $item->route = route('frontend.berita.show', $item->id);
                $item->excerpt = Str::limit($item->ringkasan ?? $item->isi, 150);
                $item->icon = 'fas fa-newspaper';
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Search in Layanan Disabilitas
     */
    private function searchDisabilitas($query)
    {
        if (!class_exists('App\Models\LayananDisabilitas')) {
            return collect();
        }
        
        try {
            $results = LayananDisabilitas::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                ->orWhere('konten', 'LIKE', "%{$query}%")
                ->orWhere('nomor', 'LIKE', "%{$query}%")
                ->orWhere('tahun', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'disabilitas';
                $item->search_category = 'layanan disabilitas';
                $item->route = route('frontend.layanan-disabilitas.show', $item->id);
                $item->excerpt = Str::limit($item->deskripsi ?? $item->konten, 150);
                $item->icon = 'fas fa-wheelchair';
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Search in Pembentukan PUU
     */
    private function searchPembentukanPuu($query)
    {
        if (!class_exists('App\Models\PembentukanPuu')) {
            return collect();
        }
        
        try {
            $results = PembentukanPuu::where('judul', 'LIKE', "%{$query}%")
                ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                ->orWhere('konten', 'LIKE', "%{$query}%")
                ->orWhere('nomor', 'LIKE', "%{$query}%")
                ->orWhere('tahun', 'LIKE', "%{$query}%")
                ->get();
            
            $results->each(function($item) {
                $item->search_type = 'pembentukan_puu';
                $item->search_category = 'pembentukan puu';
                $item->route = route('frontend.pembentukan-puu.show', $item->id);
                $item->excerpt = Str::limit($item->deskripsi ?? $item->konten, 150);
                $item->icon = 'fas fa-file-contract';
            });
            
            return $results;
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    /**
     * Helper: Sort results by relevance
     */
    private function sortResultsByRelevance($results, $query)
    {
        return $results->sortByDesc(function($item) use ($query) {
            $score = 0;
            $lowerQuery = strtolower($query);
            
            // Prioritas judul (10 poin)
            if (stripos($item->judul, $query) !== false) {
                $score += 10;
            }
            
            // Prioritas kata kunci di judul (5 poin per kata)
            $words = explode(' ', $lowerQuery);
            foreach ($words as $word) {
                if (strlen($word) > 2 && stripos($item->judul, $word) !== false) {
                    $score += 5;
                }
            }
            
            // Prioritas deskripsi/ringkasan (3 poin)
            $description = $item->deskripsi ?? $item->ringkasan ?? $item->isi ?? $item->konten ?? '';
            if (stripos($description, $query) !== false) {
                $score += 3;
            }
            
            // Prioritas berdasarkan tanggal (lebih baru = lebih relevan)
            if (isset($item->created_at)) {
                $daysOld = now()->diffInDays($item->created_at);
                if ($daysOld < 30) $score += 3; // Kurang dari 1 bulan
                elseif ($daysOld < 365) $score += 1; // Kurang dari 1 tahun
            }
            
            return $score;
        });
    }
    
    /**
     * Helper: Get category counts for filter
     */
    private function getCategoryCounts($results)
    {
        $categories = [
            'semua' => $results->count(),
            'dokumen' => $results->where('search_type', 'dokumen')->count(),
            'pengumuman' => $results->where('search_type', 'pengumuman')->count(),
            'informasi_hukum' => $results->where('search_type', 'informasi_hukum')->count(),
            'berita' => $results->where('search_type', 'berita')->count(),
            'disabilitas' => $results->where('search_type', 'disabilitas')->count(),
            'pembentukan_puu' => $results->where('search_type', 'pembentukan_puu')->count(),
        ];
        
        return array_filter($categories, function($count) {
            return $count > 0;
        });
    }
    
    /**
     * Helper: Get kategori parameter from category name
     */
    private function getKategoriFromCategory($category)
    {
        $mapping = [
            'peraturan' => 'peraturan',
            'monografi' => 'monografi',
            'artikel' => 'artikel',
            'putusan' => 'putusan',
            'pengumuman' => 'pengumuman',
            'informasi hukum' => 'informasi'
        ];
        
        return $mapping[$category] ?? 'peraturan';
    }
    
    /**
     * Helper: Get icon for dokumen category
     */
    private function getIconForCategory($kategori)
    {
        $icons = [
            'peraturan' => 'fas fa-file-alt',
            'monografi' => 'fas fa-book',
            'artikel' => 'fas fa-newspaper',
            'putusan' => 'fas fa-gavel',
            'pengumuman' => 'fas fa-bullhorn',
            'informasi' => 'fas fa-info-circle',
        ];
        
        return $icons[$kategori] ?? 'fas fa-file';
    }
    
    /**
     * API Search for AJAX requests (optional)
     */
    public function apiSearch(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['results' => []]);
        }
        
        $results = collect();
        
        // Search in different categories
        $results = $results->merge($this->searchDokumen($query));
        $results = $results->merge($this->searchPengumuman($query));
        $results = $results->merge($this->searchInformasiHukum($query));
        $results = $results->merge($this->searchBerita($query));
        $results = $results->merge($this->searchDisabilitas($query));
        $results = $results->merge($this->searchPembentukanPuu($query));
        
        // Limit results for API
        $limitedResults = $results->take(10)->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->judul,
                'type' => $item->search_type,
                'category' => $item->search_category,
                'excerpt' => $item->excerpt ?? Str::limit($item->deskripsi ?? '', 100),
                'url' => $item->route,
                'icon' => $item->icon ?? 'fas fa-file',
                'year' => $item->tahun ?? null,
            ];
        });
        
        return response()->json([
            'query' => $query,
            'results' => $limitedResults,
            'total' => $results->count()
        ]);
    }
}