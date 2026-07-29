<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PembentukanPuu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembentukanPuuController extends Controller
{
    // Halaman index/list
    public function index(Request $request)
    {
        $query = PembentukanPuu::query();
        
        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('nomor_dokumen', 'like', '%' . $search . '%')
                  ->orWhere('lembaga_pemrakarsa', 'like', '%' . $search . '%')
                  ->orWhere('kata_kunci', 'like', '%' . $search . '%')
                  ->orWhere('penulis', 'like', '%' . $search . '%');
            });
        }
        
        // Filter jenis dokumen
        if ($request->has('jenis_dokumen') && $request->jenis_dokumen != '') {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }
        
        // Filter status publikasi
        if ($request->has('status_publikasi') && $request->status_publikasi != '') {
            $query->where('status_publikasi', $request->status_publikasi);
        }
        
        // Filter hak akses
        if ($request->has('hak_akses') && $request->hak_akses != '') {
            $query->where('hak_akses', $request->hak_akses);
        }
        
        // Filter tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }
        
        // Sorting
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $puu = $query->paginate(20);
        
        // Data untuk filter
        $jenisDokumenList = [
            'naskah_akademik' => 'Naskah Akademik',
            'naskah_keterangan_penjelasan' => 'Naskah Keterangan dan/atau Penjelasan',
            'rancangan_puu' => 'Rancangan PUU',
            'penelitian_hukum' => 'Penelitian Hukum',
            'pengkajian_hukum' => 'Pengkajian Hukum',
            'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
            'analisis_evaluasi' => 'Analisis Evaluasi',
        ];
        
        return view('backend.pembentukan-puu.index', compact('puu', 'jenisDokumenList'));
    }
    
    // Halaman create
    public function create()
    {
        $title = 'Tambah Data Pembentukan PUU';
        $puu = new PembentukanPuu();
        
        return view('backend.pembentukan-puu.create', compact('title', 'puu'));
    }
    
    // Store data baru
    public function store(Request $request)
    {
        // Validasi
        $validated = $this->validateRequest($request);
        
        // Handle file uploads
        $validated = $this->handleFileUploads($request, $validated);
        
        // Set metadata
        $validated['pengunggah'] = $request->pengunggah ?? (auth()->user()->username ?? 'admin');
        
        // Set default nilai untuk field spesifik berdasarkan jenis dokumen
        $validated = $this->setDefaultFieldValues($request, $validated);
        
        // Simpan ke database
        try {
            DB::beginTransaction();
            
            $puu = PembentukanPuu::create($validated);
            
            DB::commit();
            
            return redirect()->route('backend.pembentukan-puu.index')
                ->with('success', 'Data Pembentukan PUU berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file yang sudah diupload jika ada error
            if (isset($validated['dokumen_utama'])) {
                Storage::disk('public')->delete($validated['dokumen_utama']);
            }
            if (isset($validated['cover'])) {
                Storage::disk('public')->delete($validated['cover']);
            }
            if (isset($validated['lampiran'])) {
                Storage::disk('public')->delete($validated['lampiran']);
            }
            
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    
    // Halaman show/detail
    public function show($id)
    {
        $puu = PembentukanPuu::findOrFail($id);
        $title = 'Detail ' . $puu->judul;
        
        return view('backend.pembentukan-puu.show', compact('puu', 'title'));
    }
    
    // Halaman edit
    public function edit($id)
    {
        $puu = PembentukanPuu::findOrFail($id);
        $title = 'Edit Data Pembentukan PUU';
        
        return view('backend.pembentukan-puu.edit', compact('puu', 'title'));
    }
    
    // Update data
    public function update(Request $request, $id)
    {
        $puu = PembentukanPuu::findOrFail($id);
        
        // Validasi
        $validated = $this->validateRequest($request, $puu);
        
        // Handle file uploads
        $validated = $this->handleFileUploads($request, $validated, $puu);
        
        // Update pengunggah dari form (tanggal_unggah sudah dinormalkan di validateRequest)
        if ($request->has('pengunggah')) {
            $validated['pengunggah'] = $request->pengunggah;
        }

        // Set default nilai untuk field spesifik berdasarkan jenis dokumen
        $validated = $this->setDefaultFieldValues($request, $validated);
        
        try {
            DB::beginTransaction();
            
            $puu->update($validated);
            
            DB::commit();
            
            return redirect()->route('backend.pembentukan-puu.index')
                ->with('success', 'Data Pembentukan PUU berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
    
    // Delete data
    public function destroy($id)
    {
        $puu = PembentukanPuu::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Hapus file
            if ($puu->dokumen_utama) {
                Storage::disk('public')->delete($puu->dokumen_utama);
            }
            if ($puu->cover) {
                Storage::disk('public')->delete($puu->cover);
            }
            if ($puu->lampiran) {
                Storage::disk('public')->delete($puu->lampiran);
            }
            
            $puu->delete();
            
            DB::commit();
            
            return redirect()->route('backend.pembentukan-puu.index')
                ->with('success', 'Data Pembentukan PUU berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    // Fungsi validasi
    private function validateRequest($request, $puu = null)
    {
        $rules = [
            // Informasi Dasar
            'jenis_dokumen' => 'required|string|max:50',
            'judul' => 'required|string|max:500',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'lembaga_pemrakarsa' => 'required|string|max:200',
            'status_dokumen' => 'required|string|max:50',
            'tahapan_pembentukan' => 'nullable|string|max:50',
            
            // Informasi Umum
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string|max:500',
            'penulis' => 'nullable|string|max:300',
            'editor' => 'nullable|string|max:300',
            
            // Pengelolaan Dokumen
            'status_publikasi' => 'required|string|in:draft,published,archived',
            'hak_akses' => 'required|string|in:private,public',
            'kategori' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            
            // Informasi Administrasi
            'pengunggah' => 'required|string|max:100',
            'tanggal_unggah' => 'required|date',
            
            // File upload (dokumen utama hanya required untuk create)
            'dokumen_utama' => ($puu ? 'nullable' : 'required') . '|file|mimes:pdf|max:10240',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:5120',
        ];
        
        // Field spesifik berdasarkan jenis dokumen (semua nullable)
        $jenisDokumen = $request->jenis_dokumen;
        
        switch ($jenisDokumen) {
            case 'naskah_akademik':
                $rules['rumusan_masalah'] = 'nullable|string';
                $rules['tujuan_penelitian'] = 'nullable|string';
                $rules['metodologi_penelitian'] = 'nullable|string';
                $rules['tim_penyusun'] = 'nullable|string|max:500';
                $rules['tanggal_penyelesaian'] = 'nullable|date';
                break;
                
            case 'rancangan_puu':
                $rules['jenis_rancangan'] = 'nullable|string|max:50';
                $rules['prolegnas'] = 'nullable|integer';
                $rules['inisiator'] = 'nullable|string|max:50';
                $rules['pansus_panja'] = 'nullable|string|max:200';
                $rules['tanggal_pengajuan'] = 'nullable|date';
                break;
                
            case 'penelitian_hukum':
                $rules['latar_belakang'] = 'nullable|string';
                $rules['fokus_penelitian'] = 'nullable|string';
                $rules['hasil_penelitian'] = 'nullable|string';
                $rules['rekomendasi'] = 'nullable|string';
                $rules['lokasi_penelitian'] = 'nullable|string|max:200';
                break;
                
            case 'pengkajian_hukum':
                $rules['objek_pengkajian'] = 'nullable|string';
                $rules['jenis_pengkajian'] = 'nullable|string|max:50';
                $rules['tujuan_pengkajian'] = 'nullable|string';
                $rules['kesimpulan_pengkajian'] = 'nullable|string';
                $rules['tanggal_pengkajian'] = 'nullable|date';
                break;
                
            case 'pengkajian_konstitusi':
                $rules['aspek_konstitusi'] = 'nullable|string';
                $rules['jenis_pengkajian_konstitusi'] = 'nullable|string|max:50';
                $rules['dasar_hukum_pengkajian'] = 'nullable|string';
                $rules['instansi_pengkaji'] = 'nullable|string|max:200';
                $rules['implikasi_konstitusional'] = 'nullable|string';
                break;
                
            case 'analisis_evaluasi':
                $rules['objek_evaluasi'] = 'nullable|string';
                $rules['metode_evaluasi'] = 'nullable|string|max:50';
                $rules['indikator_evaluasi'] = 'nullable|string';
                $rules['temuan_evaluasi'] = 'nullable|string';
                $rules['rekomendasi_perbaikan'] = 'nullable|string';
                $rules['periode_evaluasi'] = 'nullable|date';
                break;
        }
        
        return $request->validate($rules);
    }
    
    // Fungsi handle file upload
    private function handleFileUploads($request, $validated, $puu = null)
    {
        // Dokumen utama
        if ($request->hasFile('dokumen_utama')) {
            // Hapus file lama jika ada (untuk update)
            if ($puu && $puu->dokumen_utama) {
                Storage::disk('public')->delete($puu->dokumen_utama);
            }
            
            $path = $request->file('dokumen_utama')->store('pembentukan-puu/dokumen', 'public');
            $validated['dokumen_utama'] = $path;
        } elseif ($request->has('hapus_dokumen_utama') && $request->hapus_dokumen_utama == '1') {
            // Jika checkbox hapus dicentang
            if ($puu && $puu->dokumen_utama) {
                Storage::disk('public')->delete($puu->dokumen_utama);
            }
            $validated['dokumen_utama'] = null;
        }
        
        // Cover
        if ($request->hasFile('cover')) {
            if ($puu && $puu->cover) {
                Storage::disk('public')->delete($puu->cover);
            }
            
            $path = $request->file('cover')->store('pembentukan-puu/cover', 'public');
            $validated['cover'] = $path;
        } elseif ($request->has('hapus_cover') && $request->hapus_cover == '1') {
            if ($puu && $puu->cover) {
                Storage::disk('public')->delete($puu->cover);
            }
            $validated['cover'] = null;
        }
        
        // Lampiran
        if ($request->hasFile('lampiran')) {
            if ($puu && $puu->lampiran) {
                Storage::disk('public')->delete($puu->lampiran);
            }
            
            $path = $request->file('lampiran')->store('pembentukan-puu/lampiran', 'public');
            $validated['lampiran'] = $path;
        } elseif ($request->has('hapus_lampiran') && $request->hapus_lampiran == '1') {
            if ($puu && $puu->lampiran) {
                Storage::disk('public')->delete($puu->lampiran);
            }
            $validated['lampiran'] = null;
        }
        
        return $validated;
    }
    
    // Fungsi untuk set default nilai field berdasarkan jenis dokumen
    private function setDefaultFieldValues($request, $validated)
    {
        $jenisDokumen = $request->jenis_dokumen;
        
        // Reset semua field spesifik menjadi null
        $fieldGroups = [
            'naskah_akademik' => ['rumusan_masalah', 'tujuan_penelitian', 'metodologi_penelitian', 'tim_penyusun', 'tanggal_penyelesaian'],
            'rancangan_puu' => ['jenis_rancangan', 'prolegnas', 'inisiator', 'pansus_panja', 'tanggal_pengajuan'],
            'penelitian_hukum' => ['latar_belakang', 'fokus_penelitian', 'hasil_penelitian', 'rekomendasi', 'lokasi_penelitian'],
            'pengkajian_hukum' => ['objek_pengkajian', 'jenis_pengkajian', 'tujuan_pengkajian', 'kesimpulan_pengkajian', 'tanggal_pengkajian'],
            'pengkajian_konstitusi' => ['aspek_konstitusi', 'jenis_pengkajian_konstitusi', 'dasar_hukum_pengkajian', 'instansi_pengkaji', 'implikasi_konstitusional'],
            'analisis_evaluasi' => ['objek_evaluasi', 'metode_evaluasi', 'indikator_evaluasi', 'temuan_evaluasi', 'rekomendasi_perbaikan', 'periode_evaluasi'],
        ];
        
        // Set nilai untuk jenis dokumen yang dipilih
        if (isset($fieldGroups[$jenisDokumen])) {
            foreach ($fieldGroups[$jenisDokumen] as $field) {
                if (!isset($validated[$field])) {
                    $validated[$field] = $request->$field ?? null;
                }
            }
        }
        
        return $validated;
    }
    
    // Download file
    public function download($id, $type)
    {
        $puu = PembentukanPuu::findOrFail($id);
        
        $filePath = '';
        $fileName = '';
        
        switch ($type) {
            case 'dokumen':
                $filePath = $puu->dokumen_utama;
                $fileName = 'dokumen_' . str_replace(' ', '_', $puu->judul) . '.pdf';
                break;
            case 'cover':
                $filePath = $puu->cover;
                $fileName = 'cover_' . str_replace(' ', '_', $puu->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            case 'lampiran':
                $filePath = $puu->lampiran;
                $fileName = 'lampiran_' . str_replace(' ', '_', $puu->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            default:
                return back()->with('error', 'Tipe file tidak valid.');
        }
        
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }
        
        // Update counter download
        $puu->increment('jumlah_download');
        
        return Storage::disk('public')->download($filePath, $fileName);
    }
}
