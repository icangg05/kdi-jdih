<?php
// app/Http\Controllers\Backend\DisabilitasController.php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Disabilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisabilitasController extends Controller
{
    // Halaman index/list (DAFTAR DATA dalam table) dengan fitur pencarian
    public function index(Request $request)
    {
        $query = Disabilitas::query();
        
        // Pencarian berdasarkan keyword
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('nomor_dokumen', 'like', '%' . $search . '%')
                  ->orWhere('lembaga_penetap', 'like', '%' . $search . '%')
                  ->orWhere('jenis_dokumen', 'like', '%' . $search . '%')
                  ->orWhere('kata_kunci', 'like', '%' . $search . '%')
                  ->orWhere('penulis', 'like', '%' . $search . '%')
                  ->orWhere('tempat_penetapan', 'like', '%' . $search . '%');
            });
        }
        
        // Filter status publikasi
        if ($request->has('status') && $request->status != '') {
            $query->where('status_publikasi', $request->status);
        }
        
        // Filter hak akses
        if ($request->has('hak_akses') && $request->hak_akses != '') {
            $query->where('hak_akses', $request->hak_akses);
        }
        
        // Filter tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }
        
        // Filter jenis dokumen
        if ($request->has('jenis_dokumen') && $request->jenis_dokumen != '') {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }
        
        // Filter ruang lingkup
        if ($request->has('ruang_lingkup') && $request->ruang_lingkup != '') {
            $query->where('ruang_lingkup', $request->ruang_lingkup);
        }
        
        // Sorting default
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $disabilitas = $query->paginate(20)->appends($request->query());
        
        // Untuk filter dropdown (jika diperlukan di view)
        $jenisDokumenList = Disabilitas::distinct()->pluck('jenis_dokumen')->filter()->sort();
        $ruangLingkupList = Disabilitas::distinct()->pluck('ruang_lingkup')->filter()->sort();
        $tahunList = Disabilitas::distinct()->pluck('tahun')->filter()->sort()->values()->toArray();
        
        return view('backend.disabilitas.index', compact('disabilitas', 'jenisDokumenList', 'ruangLingkupList', 'tahunList'));
    }

    // Halaman show/detail (menggunakan template detail dengan tabs)
    public function show($id)
    {
        $disabilitas = Disabilitas::findOrFail($id);
        $title = $disabilitas->judul ?? 'Detail Disabilitas';
        
        return view('backend.disabilitas.show', compact('disabilitas', 'title'));
    }

    // Halaman create (form tambah data)
    public function create()
    {
        $title = 'Tambah Data Disabilitas';
        return view('backend.disabilitas.create', compact('title'));
    }

    // Store data
    public function store(Request $request)
    {
        // Validasi untuk semua field yang ada di database
        $validated = $request->validate([
            // Informasi Dasar Dokumen
            'jenis_dokumen' => 'required|string|max:50',
            'judul' => 'required|string|max:500',
            'nomor_dokumen' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'tempat_penetapan' => 'nullable|string|max:100',
            'tanggal_penetapan' => 'nullable|string',
            'lembaga_penetap' => 'required|string|max:200',
            'status_dokumen' => 'required|string|max:50',
            'dokumen_terkait' => 'nullable|string|max:200',
            
            // Konten Dokumen
            'ruang_lingkup' => 'required|string|max:50',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string|max:255',
            'jumlah_halaman' => 'nullable|integer|min:1',
            'bahasa' => 'nullable|string|max:50',
            
            // File Uploads
            'dokumen_utama' => 'required|file|mimes:pdf|max:10240',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            
            // Informasi Tambahan
            'penulis' => 'nullable|string|max:200',
            'penerbit' => 'nullable|string|max:200',
            'isbn_issn' => 'nullable|string|max:50',
            'doi' => 'nullable|string|max:100',
            'sumber' => 'nullable|string|max:200',
            'url_referensi' => 'nullable|url|max:500',
            
            // Hak Akses dan Status
            'status_publikasi' => 'required|string|in:draft,published',
            'hak_akses' => 'required|string|in:private,public',
            'tanggal_unggah' => 'required|string',
            'keterangan' => 'nullable|string',
            
            // Untuk kompatibilitas dengan FrontendController
            'abstract' => 'nullable|string', // alias untuk 'abstrak'
            'file_path' => 'nullable|file|mimes:pdf|max:10240', // alias untuk 'dokumen_utama'
        ]);

        // Handle checkbox arrays untuk jenis disabilitas
        $jenisDisabilitas = $request->jenis_disabilitas ?? [];
        $validated['jenis_disabilitas'] = json_encode($jenisDisabilitas);

        // Handle checkbox arrays untuk sektor kebijakan
        $sektorKebijakan = $request->sektor_kebijakan ?? [];
        $validated['sektor_kebijakan'] = json_encode($sektorKebijakan);

        // Handle file uploads
        if ($request->hasFile('dokumen_utama')) {
            $validated['dokumen_utama'] = $request->file('dokumen_utama')->store('disabilitas/dokumen', 'public');
            // Untuk kompatibilitas dengan FrontendController
            $validated['file_path'] = $validated['dokumen_utama'];
        }

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('disabilitas/cover', 'public');
        }

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('disabilitas/lampiran', 'public');
        }

        // Set default values
        $validated['pengunggah'] = auth()->user()->username ?? 'admin';
        
        // TANGGAL UNGGAH - SIMPLIFIED PARSING
        if (!empty($request->tanggal_unggah)) {
            $validated['tanggal_unggah'] = $this->parseDate($request->tanggal_unggah);
            \Log::info('Store - Tanggal unggah: dari "' . $request->tanggal_unggah . '" ke "' . $validated['tanggal_unggah'] . '"');
        } else {
            $validated['tanggal_unggah'] = Carbon::now()->format('Y-m-d');
        }

        // TANGGAL PENETAPAN - SIMPLIFIED PARSING
        if (!empty($request->tanggal_penetapan)) {
            $validated['tanggal_penetapan'] = $this->parseDate($request->tanggal_penetapan, true);
        }

        // Handle alias fields untuk kompatibilitas
        if (!empty($request->abstract)) {
            $validated['abstrak'] = $request->abstract;
        }
        
        if ($request->hasFile('file_path')) {
            $validated['dokumen_utama'] = $request->file('file_path')->store('disabilitas/dokumen', 'public');
            $validated['file_path'] = $validated['dokumen_utama'];
        }

        // Create record
        $document = Disabilitas::create($validated);

        return redirect()->route('backend.disabilitas.index')
            ->with('success', 'Dokumen disabilitas berhasil ditambahkan.');
    }

    // Halaman edit
    public function edit($id)
    {
        $disabilitas = Disabilitas::findOrFail($id);
        $title = 'Edit Data Disabilitas';
        
        // Tanggal diformat oleh accessor *_formatted di model Disabilitas.

        // Decode JSON untuk checkbox
        return view('backend.disabilitas.edit', compact('disabilitas', 'title'));
    }

    // Update data - FIXED PARSING
    public function update(Request $request, $id)
    {
        $document = Disabilitas::findOrFail($id);
        
        \Log::info('=== UPDATE METHOD ID: ' . $id . ' ===');
        \Log::info('Input tanggal_unggah: ' . $request->tanggal_unggah);
        \Log::info('Current DB tanggal_unggah: ' . $document->tanggal_unggah);
        \Log::info('Input tanggal_penetapan: ' . $request->tanggal_penetapan);
        \Log::info('Current DB tanggal_penetapan: ' . $document->tanggal_penetapan);
        
        $validated = $request->validate([
            // Informasi Dasar Dokumen
            'jenis_dokumen' => 'required|string|max:50',
            'judul' => 'required|string|max:500',
            'nomor_dokumen' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'tempat_penetapan' => 'nullable|string|max:100',
            'tanggal_penetapan' => 'nullable|string',
            'lembaga_penetap' => 'required|string|max:200',
            'status_dokumen' => 'required|string|max:50',
            'dokumen_terkait' => 'nullable|string|max:200',
            
            // Konten Dokumen
            'ruang_lingkup' => 'required|string|max:50',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string|max:255',
            'jumlah_halaman' => 'nullable|integer|min:1',
            'bahasa' => 'nullable|string|max:50',
            
            // File Uploads
            'dokumen_utama' => 'nullable|file|mimes:pdf|max:10240',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            
            // Informasi Tambahan
            'penulis' => 'nullable|string|max:200',
            'penerbit' => 'nullable|string|max:200',
            'isbn_issn' => 'nullable|string|max:50',
            'doi' => 'nullable|string|max:100',
            'sumber' => 'nullable|string|max:200',
            'url_referensi' => 'nullable|url|max:500',
            
            // Hak Akses dan Status
            'status_publikasi' => 'required|string|in:draft,published',
            'hak_akses' => 'required|string|in:private,public',
            'tanggal_unggah' => 'required|string',
            'keterangan' => 'nullable|string',
            
            // Untuk kompatibilitas
            'abstract' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Handle checkbox arrays
        $jenisDisabilitas = $request->jenis_disabilitas ?? [];
        $validated['jenis_disabilitas'] = json_encode($jenisDisabilitas);

        $sektorKebijakan = $request->sektor_kebijakan ?? [];
        $validated['sektor_kebijakan'] = json_encode($sektorKebijakan);

        // TANGGAL UNGGAH - FIXED PARSING
        if (!empty($request->tanggal_unggah)) {
            $parsedDate = $this->parseDate($request->tanggal_unggah);
            if ($parsedDate) {
                $validated['tanggal_unggah'] = $parsedDate;
                \Log::info('UPDATE SUCCESS - Tanggal unggah: dari "' . $request->tanggal_unggah . '" ke "' . $validated['tanggal_unggah'] . '"');
            } else {
                $validated['tanggal_unggah'] = $document->tanggal_unggah;
                \Log::warning('UPDATE FAILED - Parsing failed, keeping old: ' . $validated['tanggal_unggah']);
            }
        } else {
            $validated['tanggal_unggah'] = $document->tanggal_unggah;
        }

        // TANGGAL PENETAPAN - FIXED PARSING
        if (!empty($request->tanggal_penetapan)) {
            $parsedDate = $this->parseDate($request->tanggal_penetapan, true);
            if ($parsedDate) {
                $validated['tanggal_penetapan'] = $parsedDate;
                \Log::info('UPDATE SUCCESS - Tanggal penetapan: dari "' . $request->tanggal_penetapan . '" ke "' . $validated['tanggal_penetapan'] . '"');
            } else {
                $validated['tanggal_penetapan'] = null;
            }
        } else {
            $validated['tanggal_penetapan'] = $document->tanggal_penetapan;
        }

        // Handle file uploads (update only if new file uploaded)
        if ($request->hasFile('dokumen_utama')) {
            // Delete old file
            if ($document->dokumen_utama) {
                Storage::disk('public')->delete($document->dokumen_utama);
            }
            $validated['dokumen_utama'] = $request->file('dokumen_utama')->store('disabilitas/dokumen', 'public');
            $validated['file_path'] = $validated['dokumen_utama'];
        } else {
            $validated['dokumen_utama'] = $document->dokumen_utama;
            $validated['file_path'] = $document->dokumen_utama;
        }

        if ($request->hasFile('cover')) {
            if ($document->cover) {
                Storage::disk('public')->delete($document->cover);
            }
            $validated['cover'] = $request->file('cover')->store('disabilitas/cover', 'public');
        } else {
            $validated['cover'] = $document->cover;
        }

        if ($request->hasFile('lampiran')) {
            if ($document->lampiran) {
                Storage::disk('public')->delete($document->lampiran);
            }
            $validated['lampiran'] = $request->file('lampiran')->store('disabilitas/lampiran', 'public');
        } else {
            $validated['lampiran'] = $document->lampiran;
        }

        // Handle delete file checkboxes
        if ($request->has('hapus_dokumen_utama') && $request->hapus_dokumen_utama == '1') {
            if ($document->dokumen_utama) {
                Storage::disk('public')->delete($document->dokumen_utama);
            }
            $validated['dokumen_utama'] = null;
            $validated['file_path'] = null;
        }

        if ($request->has('hapus_cover') && $request->hapus_cover == '1') {
            if ($document->cover) {
                Storage::disk('public')->delete($document->cover);
            }
            $validated['cover'] = null;
        }

        if ($request->has('hapus_lampiran') && $request->hapus_lampiran == '1') {
            if ($document->lampiran) {
                Storage::disk('public')->delete($document->lampiran);
            }
            $validated['lampiran'] = null;
        }

        // Handle alias fields untuk kompatibilitas
        if ($request->hasFile('file_path')) {
            if ($document->dokumen_utama) {
                Storage::disk('public')->delete($document->dokumen_utama);
            }
            $validated['dokumen_utama'] = $request->file('file_path')->store('disabilitas/dokumen', 'public');
            $validated['file_path'] = $validated['dokumen_utama'];
        }
        
        if (!empty($request->abstract)) {
            $validated['abstrak'] = $request->abstract;
        }

        // Debug sebelum update
        \Log::info('=== BEFORE UPDATE ===');
        \Log::info('Old tanggal_unggah: ' . $document->tanggal_unggah);
        \Log::info('New tanggal_unggah: ' . $validated['tanggal_unggah']);
        \Log::info('Old tanggal_penetapan: ' . $document->tanggal_penetapan);
        \Log::info('New tanggal_penetapan: ' . $validated['tanggal_penetapan']);
        
        // Update record
        $document->update($validated);
        
        // Verifikasi update
        $updatedDocument = Disabilitas::find($id);
        \Log::info('=== AFTER UPDATE ===');
        \Log::info('Updated tanggal_unggah: ' . $updatedDocument->tanggal_unggah);
        \Log::info('Updated tanggal_penetapan: ' . $updatedDocument->tanggal_penetapan);

        return redirect()->route('backend.disabilitas.index')
            ->with('success', 'Dokumen disabilitas berhasil diperbarui.');
    }

    // ==============================================
    // DELETE METHOD YANG 100% BEKERJA
    // ==============================================
    
    /**
     * Hapus data disabilitas - VERSI SIMPLE DAN PASTI BEKERJA
     */
    public function destroy($id)
    {
        \Log::info('=== DELETE DISABILITAS === ID: ' . $id);
        
        try {
            // 1. CARI DATA TERLEBIH DAHULU
            $data = Disabilitas::find($id);
            
            if (!$data) {
                \Log::warning('Data tidak ditemukan dengan ID: ' . $id);
                return redirect()->route('backend.disabilitas.index')
                    ->with('error', 'Data tidak ditemukan!');
            }
            
            $judul = $data->judul;
            \Log::info('Menghapus data: ' . $judul);
            
            // 2. HAPUS FILE DARI STORAGE (JIKA ADA)
            // Dokumen utama
            if ($data->dokumen_utama && Storage::disk('public')->exists($data->dokumen_utama)) {
                Storage::disk('public')->delete($data->dokumen_utama);
                \Log::info('File dihapus: ' . $data->dokumen_utama);
            }
            
            // Cover
            if ($data->cover && Storage::disk('public')->exists($data->cover)) {
                Storage::disk('public')->delete($data->cover);
                \Log::info('Cover dihapus: ' . $data->cover);
            }
            
            // Lampiran
            if ($data->lampiran && Storage::disk('public')->exists($data->lampiran)) {
                Storage::disk('public')->delete($data->lampiran);
                \Log::info('Lampiran dihapus: ' . $data->lampiran);
            }
            
            // 3. HAPUS DARI DATABASE - GUNAKAN RAW QUERY UNTUK PASTI BEKERJA
            $deleted = DB::table('disabilitas')->where('id', $id)->delete();
            
            if ($deleted) {
                \Log::info('✅ Data berhasil dihapus dari database. Rows affected: ' . $deleted);
                return redirect()->route('backend.disabilitas.index')
                    ->with('success', "Data '$judul' berhasil dihapus!");
            } else {
                \Log::error('❌ Gagal menghapus data dari database');
                return redirect()->route('backend.disabilitas.index')
                    ->with('error', 'Gagal menghapus data dari database!');
            }
            
        } catch (\Exception $e) {
            \Log::error('❌ ERROR DELETE: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('backend.disabilitas.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Download file
    public function download($id, $type)
    {
        $document = Disabilitas::findOrFail($id);
        
        $filePath = '';
        $fileName = '';
        
        switch ($type) {
            case 'dokumen':
            case 'dokumen_utama':
                $filePath = $document->dokumen_utama;
                $fileName = 'dokumen_' . str_replace(' ', '_', $document->judul) . '.pdf';
                break;
            case 'cover':
                $filePath = $document->cover;
                $fileName = 'cover_' . str_replace(' ', '_', $document->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            case 'lampiran':
                $filePath = $document->lampiran;
                $fileName = 'lampiran_' . str_replace(' ', '_', $document->judul) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                break;
            default:
                return back()->with('error', 'Tipe file tidak valid.');
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($filePath, $fileName);
    }

    // View/show action untuk preview (tambahan)
    public function view($id)
    {
        $document = Disabilitas::findOrFail($id);
        $title = $document->judul ?? 'Detail Dokumen Disabilitas';
        
        return view('backend.disabilitas.view', compact('document', 'title'));
    }
    
    // ==============================================
    // HELPER METHOD UNTUK PARSING TANGGAL
    // ==============================================
    
    /**
     * Parse tanggal dari berbagai format ke Y-m-d
     * 
     * @param string $dateInput Input tanggal (contoh: "13/1/2019", "13-1-2019", "13/01/2019")
     * @param bool $nullable Jika true, return null jika parsing gagal
     * @return string|null Format Y-m-d atau null
     */
    private function parseDate($dateInput, $nullable = false)
    {
        if (empty($dateInput)) {
            return $nullable ? null : Carbon::now()->format('Y-m-d');
        }
        
        $dateInput = trim($dateInput);
        \Log::info('Parsing date: "' . $dateInput . '"');
        
        try {
            // Coba format: 13/1/2019 (j/n/Y)
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateInput)) {
                // Pisahkan bagian tanggal
                $parts = explode('/', $dateInput);
                $day = (int)$parts[0];
                $month = (int)$parts[1];
                $year = (int)$parts[2];
                
                // Validasi tanggal
                if (checkdate($month, $day, $year)) {
                    $date = Carbon::createFromDate($year, $month, $day);
                    \Log::info('Parsed as j/n/Y: ' . $date->format('Y-m-d'));
                    return $date->format('Y-m-d');
                }
            }
            
            // Coba format: 13/01/2019 (d/m/Y)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateInput)) {
                $date = Carbon::createFromFormat('d/m/Y', $dateInput);
                \Log::info('Parsed as d/m/Y: ' . $date->format('Y-m-d'));
                return $date->format('Y-m-d');
            }
            
            // Coba format: 13-1-2019 (j-n-Y)
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $dateInput)) {
                $parts = explode('-', $dateInput);
                $day = (int)$parts[0];
                $month = (int)$parts[1];
                $year = (int)$parts[2];
                
                if (checkdate($month, $day, $year)) {
                    $date = Carbon::createFromDate($year, $month, $day);
                    \Log::info('Parsed as j-n-Y: ' . $date->format('Y-m-d'));
                    return $date->format('Y-m-d');
                }
            }
            
            // Coba format: 13-01-2019 (d-m-Y)
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateInput)) {
                $date = Carbon::createFromFormat('d-m-Y', $dateInput);
                \Log::info('Parsed as d-m-Y: ' . $date->format('Y-m-d'));
                return $date->format('Y-m-d');
            }
            
            // Fallback: coba Carbon::parse
            try {
                $date = Carbon::parse($dateInput);
                \Log::info('Parsed with Carbon::parse: ' . $date->format('Y-m-d'));
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::error('Carbon::parse failed: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Error parsing date "' . $dateInput . '": ' . $e->getMessage());
        }
        
        // Jika semua gagal
        \Log::warning('All parsing attempts failed for: "' . $dateInput . '"');
        return $nullable ? null : Carbon::now()->format('Y-m-d');
    }
}
