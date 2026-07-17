<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DisabilitasController as BackendDisabilitasController;
use App\Http\Controllers\Frontend\DisabilitasController as FrontendDisabilitasController;
use App\Http\Controllers\Backend\PembentukanPuuController;
use App\Http\Controllers\Frontend\PembentukanPuuController as FrontendPembentukanPuuController;
use App\Http\Controllers\AiSearchController;
use App\Http\Controllers\Frontend\StatistikController;
use App\Http\Controllers\SurveyController;
use App\Livewire\Beranda;
use App\Livewire\Berita;
use App\Livewire\BeritaShow;
use App\Livewire\Dokumen;
use App\Livewire\DokumenShow;
use App\Livewire\InformasiHukum;
use App\Livewire\InformasiHukumShow;
use App\Livewire\Pengumuman;
use App\Livewire\PengumumanShow;
use App\Livewire\Profil;
use Illuminate\Support\Facades\Route;

// ========== AUTH ROUTES ==========
Route::get('/backend', [AuthController::class, 'login'])->middleware('guest')->name('login');
Route::post('/backend', [AuthController::class, 'authenticate'])->middleware('guest')->name('authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/download', [DashboardController::class, 'downloadFile'])->name('download_file');

Route::get('/statistik-data', [StatistikController::class, 'getStatistikData']);

Route::get('/survei-kepuasan', [SurveyController::class, 'create'])->name('survey.create');
Route::post('/survei-kepuasan', [SurveyController::class, 'store'])->name('survey.store');
Route::get('/survei-terimakasih', [SurveyController::class, 'thankyou'])->name('survey.thankyou');

// Route untuk AI Search
Route::post('/ai-search', [AiSearchController::class, 'search'])->name('ai.search');
Route::post('/ai-search/simple', [AiSearchController::class, 'searchSimple'])->name('ai.search.simple');

// ========== FRONTEND ROUTES ==========
Route::name('frontend.')->group(function () {
    // Beranda
    Route::get('/', Beranda::class)->name('beranda');
    
    // Profil
    Route::get('/profil/{kategori}', Profil::class)->name('profil');
    
    // Pengumuman
    Route::get('/pengumuman', Pengumuman::class)->name('pengumuman.index');
    Route::get('/pengumuman/{id}', PengumumanShow::class)->name('pengumuman.show');
    
    // Dokumen
    Route::get('/dokumen/{kategori}', Dokumen::class)->name('dokumen.index');
    Route::get('/dokumen/{kategori}/{id}', DokumenShow::class)->name('dokumen.show');
    
    // Informasi Hukum
    Route::get('/informasi-hukum/jenis/{id}', InformasiHukum::class)->name('informasi-hukum.index');
    Route::get('/informasi-hukum/{id}', InformasiHukumShow::class)->name('informasi-hukum.show');
    
    // Berita
    Route::get('/berita', Berita::class)->name('berita.index');
    Route::get('/berita/{id}', BeritaShow::class)->name('berita.show');
    
    // ========== LAYANAN DISABILITAS (FRONTEND) ==========
    Route::prefix('layanan-disabilitas')->name('disabilitas.')->group(function () {
        Route::get('/', [FrontendDisabilitasController::class, 'index'])->name('index');
        Route::get('/{id}', [FrontendDisabilitasController::class, 'show'])->name('show');
        Route::post('/search', [FrontendDisabilitasController::class, 'search'])->name('search');
        Route::get('/{id}/abstract', [FrontendDisabilitasController::class, 'getAbstract'])->name('abstract');
        
        // Route download dengan parameter type
        Route::get('/{id}/download/{type}', [FrontendDisabilitasController::class, 'download'])->name('download')
            ->where('type', 'dokumen|cover|lampiran');
        
        // Route alternatif untuk compatibility dengan URL lama
        Route::get('/{id}/download/dokumen', [FrontendDisabilitasController::class, 'download'])->name('download.dokumen')
            ->defaults('type', 'dokumen');
    });
    
    Route::get('/layanan-disabilitas/{id}/abstract', [FrontendDisabilitasController::class, 'getAbstract'])->name('disabilitas.abstract');
    
    // Route alternatif untuk disabilitas
    Route::get('/disabilitas', [FrontendDisabilitasController::class, 'index'])->name('disabilitas');
    
    // ========== PEMBENTUKAN PUU (FRONTEND) ==========
    Route::prefix('pembentukan-puu')->name('pembentukan-puu.')->group(function () {
        // Halaman beranda/utama
        Route::get('/', [FrontendPembentukanPuuController::class, 'index'])->name('index');
        
        // Halaman kategori spesifik
        Route::get('/kategori/{kategori}', [FrontendPembentukanPuuController::class, 'kategori'])->name('kategori');
        
        // Halaman detail dokumen
        Route::get('/detail/{id}', [FrontendPembentukanPuuController::class, 'show'])->name('show');
        
        // Route untuk download file di frontend
        Route::get('/download/{id}/{type}', [FrontendPembentukanPuuController::class, 'download'])
            ->name('download')
            ->where('type', 'dokumen|cover|lampiran');
        
        // Route untuk abstract
        Route::get('/abstract/{id}', [FrontendPembentukanPuuController::class, 'getAbstract'])->name('abstract');
        
        // Opsional: Halaman statistik
        Route::get('/statistik', [FrontendPembentukanPuuController::class, 'statistik'])->name('statistik');
        
        // Opsional: Halaman tentang
        Route::get('/tentang', [FrontendPembentukanPuuController::class, 'tentang'])->name('tentang');
    });
});

// ========== BACKEND ROUTES (PROTECTED) ==========
Route::middleware(['auth'])->prefix('dashboard')->name('backend.')->group(function () {
    
    // Dashboard utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistik-detail', [DashboardController::class, 'getStatistikDetail'])->name('statistik-detail');
    Route::get('/aktivitas-terbaru', [DashboardController::class, 'getAktivitasTerbaru'])->name('aktivitas-terbaru');
    
    // ========== DISABILITAS MANAGEMENT (BACKEND) ==========
    Route::prefix('disabilitas')->name('disabilitas.')->group(function () {
        Route::get('/', [BackendDisabilitasController::class, 'index'])->name('index');
        Route::get('/create', [BackendDisabilitasController::class, 'create'])->name('create');
        Route::post('/', [BackendDisabilitasController::class, 'store'])->name('store');
        Route::get('/{id}', [BackendDisabilitasController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BackendDisabilitasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BackendDisabilitasController::class, 'update'])->name('update');
        Route::delete('/{id}', [BackendDisabilitasController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download/{type}', [BackendDisabilitasController::class, 'download'])->name('download')
            ->where('type', 'dokumen|cover|lampiran');
    });
    
    // ========== PEMBENTUKAN PUU MANAGEMENT (BACKEND) ==========
    Route::prefix('pembentukan-puu')->name('pembentukan-puu.')->group(function () {
        
        Route::get('/', [PembentukanPuuController::class, 'index'])->name('index');
        Route::get('/create', [PembentukanPuuController::class, 'create'])->name('create');
        Route::post('/', [PembentukanPuuController::class, 'store'])->name('store');
        Route::get('/{id}', [PembentukanPuuController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PembentukanPuuController::class, 'edit'])->name('edit');
        
        // PERHATIAN: Gunakan PUT bukan PATCH
        Route::put('/{id}', [PembentukanPuuController::class, 'update'])->name('update');
        
        Route::delete('/{id}', [PembentukanPuuController::class, 'destroy'])->name('destroy');
        
        // Route untuk download file di backend
        Route::get('/{id}/download/{type}', [PembentukanPuuController::class, 'download'])
            ->name('download')
            ->where('type', 'dokumen|cover|lampiran');
        
        // Kategori-kategori spesifik (opsional)
        Route::get('/naskah-akademik', [PembentukanPuuController::class, 'naskahAkademik'])->name('naskah-akademik');
        Route::get('/rancangan', [PembentukanPuuController::class, 'rancangan'])->name('rancangan');
        Route::get('/penelitian', [PembentukanPuuController::class, 'penelitian'])->name('penelitian');
        Route::get('/pengkajian', [PembentukanPuuController::class, 'pengkajian'])->name('pengkajian');
        Route::get('/pengkajian-konstitusi', [PembentukanPuuController::class, 'pengkajianKonstitusi'])->name('pengkajian-konstitusi');
        Route::get('/analisis', [PembentukanPuuController::class, 'analisis'])->name('analisis');
    });
    
    // ========== UTILITY ROUTES ==========
    Route::get('/export-database', [DashboardController::class, 'exportDatabase'])->name('export-database');
    Route::get('/generate-wilayah', [DashboardController::class, 'generateWilayah'])->name('generate-wilayah');
});
