{{-- resources/views/frontend/disabilitas/index.blade.php --}}
@extends('components.layouts.frontend')

@section('title', 'LAYANAN DISABILITAS - JDIH Kota Kendari')
@section('description', 'Portal pencarian dokumen hukum disabilitas Pemerintah Kota Kendari')
@section('image', asset('assets/img/logo-jdih.png'))

@push('styles')
<style>
    /* Voice Panel - BISA DIGESER */
    #voicePanel {
        position: fixed;
        top: 150px;
        right: 20px;
        z-index: 9999;
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        padding: 15px;
        width: 220px;
        cursor: move;
        user-select: none;
        border: 2px solid #3b82f6;
        transition: all 0.3s ease;
    }
    
    #voicePanel.dragging {
        box-shadow: 0 10px 35px rgba(59, 130, 246, 0.3);
        opacity: 0.9;
    }
    
    .voice-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .voice-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
    }
    
    .drag-handle {
        cursor: move;
        color: #6b7280;
        padding: 5px;
    }
    
    .drag-handle:hover {
        color: #3b82f6;
    }
    
    .voice-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: .4s;
        border-radius: 24px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background-color: #3b82f6;
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    
    .voice-status {
        font-size: 0.8rem;
        color: #6b7280;
        text-align: center;
        margin-top: 5px;
    }
    
    .voice-status.active {
        color: #059669;
        font-weight: 500;
    }
    
    /* Voice Input Button */
    .voice-input-btn {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.85rem;
        width: 100%;
        margin-top: 10px;
    }
    
    .voice-input-btn:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }
    
    .voice-input-btn.listening {
        background: #dcfce7;
        border-color: #86efac;
        color: #059669;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    }
    
    /* Main Content */
    .main-search-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        margin: 100px auto 30px auto;
        max-width: 800px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }
    
    .search-title {
        color: white;
        text-align: center;
        margin-bottom: 10px;
        font-size: 2rem;
    }
    
    .search-subtitle {
        color: rgba(255,255,255,0.9);
        text-align: center;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }
    
    /* Search Box - DIPERKECIL */
    .search-box {
        position: relative;
        margin-bottom: 20px;
    }
    
    .search-input {
        width: 100%;
        padding: 12px 90px 12px 20px;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    
    .search-input:focus {
        outline: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .voice-search-btn {
        position: absolute;
        right: 50px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 6px;
        transition: color 0.2s;
        z-index: 3;
        font-size: 0.9rem;
    }
    
    .voice-search-btn:hover {
        color: #3b82f6;
    }
    
    /* Search Buttons - DIPERKECIL */
    .search-buttons {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }
    
    .search-btn {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.9rem;
        min-height: 44px;
    }
    
   .search-btn.primary {
    background-color: #28a745; /* hijau */
    color: #ffffff;
    border: none;
}

.search-btn.primary:hover {
    background-color: #218838;
    }
    
    .search-btn.secondary {
        background: rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .search-btn.secondary:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }
    
    /* Filter Section - DIPERBAIKI */
    .filter-section {
        background: rgba(255,255,255,0.95);
        backdrop-filter: none;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
        display: none;
        position: relative;
        z-index: 1;
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .filter-section.show {
        display: block;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    
    .filter-label {
        color: #1f2937;
        margin-bottom: 8px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .filter-select, .filter-input {
        padding: 10px 15px;
        border: 1px solid rgba(79, 70, 229, 0.2);
        border-radius: 8px;
        background: rgba(255,255,255,0.9);
        color: #1f2937;
        font-size: 0.9rem;
    }
    
    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: #4f46e5;
        background: white;
    }
    
    /* Documents Section */
    .documents-section {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 30px;
        color: #1f2937;
        font-size: 2rem;
        margin-top: -20px; /* Menambahkan margin negatif untuk menaikkan posisi */
        padding-top: 10px; /* Tambahkan sedikit padding atas */
    }
    
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }
    
    .document-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        position: relative;
    }
    
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    .document-card.reading {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-color: #7dd3fc;
    }
    
    .document-type {
        display: inline-block;
        padding: 5px 12px;
        background: #e0e7ff;
        color: #4f46e5;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .document-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    
    .document-meta {
        display: flex;
        justify-content: space-between;
        color: #6b7280;
        font-size: 0.85rem;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }
    
    .document-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }
    
    .action-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-align: center;
    }
    
    .abstract-btn {
        background: #f3f4f6;
        color: #1f2937;
        border: none;
        cursor: pointer;
    }
    
    .abstract-btn:hover {
        background: #e5e7eb;
    }
    
    .view-btn {
        background: #4f46e5;
        color: white;
    }
    
    .view-btn:hover {
        background: #4338ca;
    }
    
    .download-btn {
        background: #10b981;
        color: white;
        grid-column: span 2;
    }
    
    .download-btn:hover {
        background: #059669;
    }
    
    /* Abstract Modal */
    .abstract-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    
    .abstract-modal.show {
        display: flex;
    }
    
    .abstract-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    .abstract-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .abstract-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .abstract-content {
        color: #4b5563;
        line-height: 1.6;
    }
    
    /* No documents message */
    .no-documents {
        text-align: center;
        padding: 50px;
        color: #6b7280;
        background: #f9fafb;
        border-radius: 15px;
        border: 2px dashed #d1d5db;
        grid-column: 1 / -1;
    }
    
    /* Pagination */
    .pagination-container {
        margin-top: 40px;
        text-align: center;
    }
    
    .pagination {
        display: inline-flex;
        gap: 5px;
    }
    
    .page-link {
        padding: 8px 15px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .page-link.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        #voicePanel {
            width: 180px;
            right: 10px;
            top: 100px;
        }
        
        .main-search-container {
            margin: 80px 20px 20px 20px;
            padding: 20px;
        }
        
        .search-title {
            font-size: 1.5rem;
        }
        
        .search-buttons {
            flex-direction: column;
        }
        
        .documents-grid {
            grid-template-columns: 1fr;
        }
        
        .document-actions {
            grid-template-columns: 1fr;
        }
        
        .download-btn {
            grid-column: span 1;
        }
        
        .search-input {
            padding-right: 70px;
            font-size: 0.9rem;
        }
        
        .voice-search-btn {
            right: 40px;
            padding: 5px;
        }
        
        .documents-section {
            margin: 20px auto;
        }
        
        .section-title {
            margin-top: -10px;
            font-size: 1.8rem;
        }
        
        .filter-grid {
            grid-template-columns: 1fr;
        }
        
        .search-btn {
            padding: 8px;
            font-size: 0.85rem;
        }
    }
    
    /* Clear search button */
    .clear-search-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 6px;
        z-index: 3;
        font-size: 0.9rem;
        display: none;
    }
    
    .clear-search-btn:hover {
        color: #ef4444;
    }
</style>
@endpush

@section('content')
    <!-- Voice Panel yang BISA DIGESER -->
    <div id="voicePanel">
        <div class="voice-header">
            <div class="voice-title">
                <i class="fas fa-volume-up mr-2 text-blue-500"></i>
                Mode Suara
            </div>
            <div class="drag-handle">
                <i class="fas fa-arrows-alt"></i>
            </div>
        </div>
        
        <div class="voice-toggle">
            <span style="font-size: 0.9rem; color: #4b5563;">Suara Otomatis</span>
            <label class="toggle-switch">
                <input type="checkbox" id="voiceToggle">
                <span class="toggle-slider"></span>
            </label>
        </div>
        
        <div id="voiceStatusText" class="voice-status">Mati</div>
        
        <button id="testVoiceBtn" class="voice-input-btn">
            <i class="fas fa-play"></i>
            Test Suara
        </button>
    </div>

    <!-- Main Search Section -->
    <div class="main-search-container">
        <h1 class="search-title text-3xl font-bold">CARI DOKUMEN DISABILITAS</h1>
        <p class="search-subtitle">Temukan semua dokumen hukum terkait disabilitas Kota Kendari</p>
        
        <div class="search-box">
            <form id="searchForm" method="GET" action="{{ route('frontend.disabilitas.index') }}">
                <input type="text" 
                       id="mainSearchInput" 
                       class="search-input" 
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Ketik kata kunci atau gunakan suara...">
                <button type="button" id="mainVoiceBtn" class="voice-search-btn" title="Pencarian suara">
                    <i class="fas fa-microphone"></i>
                </button>
                <button type="button" id="clearSearchBtn" class="clear-search-btn" title="Hapus pencarian">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>
        
        <div class="search-buttons">
            <button type="submit" form="searchForm" id="searchNowBtn" class="search-btn primary">
                <i class="fas fa-search"></i>
                Cari Sekarang
            </button>
            <button id="toggleFilterBtn" class="search-btn secondary">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div>
        
        <!-- Filter Section (Hidden by Default) -->
        <div id="filterSection" class="filter-section">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-folder"></i>
                        Jenis Dokumen
                    </label>
                    <select id="docTypeFilter" class="filter-select" name="jenis">
                        <option value="">Semua Jenis</option>
                        <!-- Disesuaikan dengan data dari create.blade.php -->
                        <option value="uu" {{ request('jenis') == 'uu' ? 'selected' : '' }}>Undang-Undang</option>
                        <option value="pp" {{ request('jenis') == 'pp' ? 'selected' : '' }}>Peraturan Pemerintah</option>
                        <option value="perpres" {{ request('jenis') == 'perpres' ? 'selected' : '' }}>Peraturan Presiden</option>
                        <option value="permen" {{ request('jenis') == 'permen' ? 'selected' : '' }}>Peraturan Menteri</option>
                        <option value="perda" {{ request('jenis') == 'perda' ? 'selected' : '' }}>Peraturan Daerah</option>
                        <option value="keppres" {{ request('jenis') == 'keppres' ? 'selected' : '' }}>Keputusan Presiden</option>
                        <option value="kepmen" {{ request('jenis') == 'kepmen' ? 'selected' : '' }}>Keputusan Menteri</option>
                        <option value="se" {{ request('jenis') == 'se' ? 'selected' : '' }}>Surat Edaran</option>
                        <option value="juknis" {{ request('jenis') == 'juknis' ? 'selected' : '' }}>Petunjuk Teknis</option>
                        <option value="panduan" {{ request('jenis') == 'panduan' ? 'selected' : '' }}>Panduan</option>
                        <option value="laporan" {{ request('jenis') == 'laporan' ? 'selected' : '' }}>Laporan</option>
                        <option value="kajian" {{ request('jenis') == 'kajian' ? 'selected' : '' }}>Studi/Kajian</option>
                        <option value="naskah_akademik" {{ request('jenis') == 'naskah_akademik' ? 'selected' : '' }}>Naskah Akademik</option>
                        <option value="rancangan" {{ request('jenis') == 'rancangan' ? 'selected' : '' }}>Rancangan Peraturan</option>
                        <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar"></i>
                        Tahun
                    </label>
                    <select id="yearFilter" class="filter-select" name="tahun">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-hashtag"></i>
                        Nomor Dokumen
                    </label>
                    <input type="text" 
                           id="docNumberFilter" 
                           class="filter-input" 
                           name="nomor"
                           value="{{ request('nomor') }}"
                           placeholder="Contoh: 5, 12, 8">
                </div>
            </div>
            
            <div class="flex gap-2">
                <button id="applyFilterBtn" class="search-btn primary flex-1">
                    <i class="fas fa-check"></i>
                    Terapkan Filter
                </button>
                <button id="resetFilterBtn" class="search-btn secondary">
                    <i class="fas fa-redo"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Documents Results Section -->
    <div class="documents-section">
        <h2 class="section-title text-2xl font-bold">DOKUMEN DISABILITAS</h2>
        
        <div id="documentsContainer">
            @if($disabilitas->count() > 0)
                <div class="documents-grid">
                    @foreach($disabilitas as $doc)
                        <div class="document-card" data-doc-id="{{ $doc->id }}">
                            <span class="document-type">{{ $doc->jenis_dokumen_formatted }}</span>
                            <h3 class="document-title">{{ $doc->judul }}</h3>
                            <p class="text-gray-600 text-sm abstract-preview">
                                {{ $doc->abstrak ? Str::limit($doc->abstrak, 150) : 'Tidak ada abstrak' }}
                            </p>
                            <div class="document-meta">
                                <span><i class="far fa-calendar mr-1"></i> {{ $doc->tahun }}</span>
                                <span><i class="far fa-file mr-1"></i> {{ $doc->jumlah_halaman ? $doc->jumlah_halaman . ' halaman' : '-' }}</span>
                                <span><i class="fas fa-download mr-1"></i> PDF</span>
                            </div>
                            <div class="document-actions">
                                <button class="action-btn abstract-btn" data-doc-id="{{ $doc->id }}">
                                    <i class="fas fa-file-alt mr-1"></i>Abstract
                                </button>
                                <a href="{{ route('frontend.disabilitas.show', $doc->id) }}" class="action-btn view-btn">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                @if($doc->dokumen_utama)
                                    <a href="{{ route('frontend.disabilitas.download', ['id' => $doc->id, 'type' => 'dokumen']) }}" 
                                       class="action-btn download-btn" target="_blank">
                                        <i class="fas fa-download mr-1"></i>Download PDF
                                    </a>
                                @else
                                    <button class="action-btn download-btn" disabled>
                                        <i class="fas fa-download mr-1"></i>File Tidak Tersedia
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($disabilitas->hasPages())
                    <div class="pagination-container">
                        {{ $disabilitas->links('vendor.pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="no-documents">
                    <i class="fas fa-file-alt text-4xl mb-4 text-gray-400"></i>
                    <h3 class="text-xl font-medium mb-2">Belum ada dokumen</h3>
                    <p class="text-gray-600">Mulai pencarian untuk melihat dokumen disabilitas</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Abstract Modal -->
    <div id="abstractModal" class="abstract-modal">
        <div class="abstract-container">
            <div class="abstract-header">
                <h3 id="abstractModalTitle" class="abstract-title">Abstract Dokumen</h3>
                <button id="closeAbstractModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="abstractModalContent" class="abstract-content">
                <!-- Konten abstract akan dimuat di sini -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // SISTEM SUARA PEREMPUAN INDONESIA/ASIA YANG JELAS
    class IndonesiaVoiceSystem {
        constructor() {
            this.isVoiceEnabled = false;
            this.indonesianFemaleVoice = null;
            this.currentDocumentCard = null;
            this.hoverTimeout = null;
            this.debounceTime = 300; // Delay untuk hover
            
            this.init();
        }
        
        async init() {
            await this.loadVoices();
            this.setupEventListeners();
            this.loadSavedSettings();
        }
        
        async loadVoices() {
            return new Promise((resolve) => {
                const loadVoicesList = () => {
                    const voices = speechSynthesis.getVoices();
                    console.log('Suara yang tersedia:', voices);
                    
                    // Mencari suara perempuan Indonesia/Asia yang terbaik
                    this.indonesianFemaleVoice = this.findBestIndonesianVoice(voices);
                    
                    if (this.indonesianFemaleVoice) {
                        console.log('Suara perempuan Indonesia dipilih:', this.indonesianFemaleVoice.name);
                    } else {
                        console.warn('Tidak ada suara perempuan Indonesia yang ditemukan');
                    }
                    
                    resolve();
                };
                
                if (speechSynthesis.getVoices().length > 0) {
                    loadVoicesList();
                } else {
                    speechSynthesis.onvoiceschanged = loadVoicesList;
                }
            });
        }
        
        // Mencari suara perempuan Indonesia/Asia terbaik
        findBestIndonesianVoice(voices) {
            const voicePriority = [
                // 1. Suara perempuan Indonesia spesifik
                v => (v.lang === 'id-ID' || v.lang.startsWith('id')) && 
                     (v.name.toLowerCase().includes('perempuan') || 
                      v.name.toLowerCase().includes('indonesian female') ||
                      v.name.toLowerCase().includes('id-female')),
                
                // 2. Suara Google Indonesia
                v => v.name.includes('Google') && v.lang.startsWith('id'),
                
                // 3. Suara perempuan Asia lainnya (Malaysia, Singapore)
                v => (v.lang === 'ms-MY' || v.lang === 'ms') && 
                     v.name.toLowerCase().includes('female'),
                
                // 4. Suara perempuan dengan aksen Asia
                v => (v.lang === 'en-GB' || v.lang === 'en-AU') && 
                     v.name.toLowerCase().includes('female'),
                
                // 5. Suara perempuan apa saja (hindari suara laki-laki)
                v => v.name.toLowerCase().includes('female') && 
                     !v.name.toLowerCase().includes('male') &&
                     !v.name.toLowerCase().includes('david') &&
                     !v.name.toLowerCase().includes('mark')
            ];
            
            for (const priority of voicePriority) {
                const voice = voices.find(priority);
                if (voice) return voice;
            }
            
            // Fallback: suara pertama yang ada
            return voices.length > 0 ? voices[0] : null;
        }
        
        setupEventListeners() {
            // Auto-read pada hover dokumen dengan debounce
            document.addEventListener('mouseover', (e) => {
                if (!this.isVoiceEnabled) return;
                
                const card = e.target.closest('.document-card');
                if (card && card !== this.currentDocumentCard) {
                    clearTimeout(this.hoverTimeout);
                    
                    this.hoverTimeout = setTimeout(() => {
                        this.currentDocumentCard = card;
                        this.readDocumentCard(card);
                    }, this.debounceTime);
                }
            });
            
            document.addEventListener('mouseout', (e) => {
                if (e.target.closest('.document-card')) {
                    clearTimeout(this.hoverTimeout);
                    setTimeout(() => {
                        this.currentDocumentCard = null;
                    }, 100);
                }
            });
            
            // Voice toggle event
            document.getElementById('voiceToggle').addEventListener('change', (e) => {
                this.toggleVoice();
            });
            
            // Test voice button
            document.getElementById('testVoiceBtn').addEventListener('click', () => {
                if (this.isVoiceEnabled) {
                    this.speak('Selamat datang di layanan dokumen disabilitas');
                } else {
                    alert('Aktifkan mode suara terlebih dahulu');
                }
            });
        }
        
        readDocumentCard(card) {
            const title = card.querySelector('.document-title')?.textContent || '';
            const type = card.querySelector('.document-type')?.textContent || '';
            const abstract = card.querySelector('.abstract-preview')?.textContent || '';
            const year = card.querySelector('.document-meta span:first-child')?.textContent?.replace('far fa-calendar mr-1', '').trim() || '';
            
            // Bangun teks dengan bahasa Indonesia yang natural dan jelas
            let speechText = '';
            
            if (type) speechText += `Dokumen ${type}. `;
            if (title) speechText += `Judul: ${title}. `;
            if (abstract && abstract !== 'Tidak ada abstrak') {
                speechText += `Abstrak: ${abstract}. `;
            }
            if (year) speechText += `Tahun ${year}. `;
            
            speechText += 'Silakan klik untuk melihat detail.';
            
            this.speak(speechText);
        }
        
        speak(text) {
            if (!this.isVoiceEnabled || !text || !window.speechSynthesis) return;
            
            // Hentikan semua suara yang sedang berjalan
            this.stopAllSpeech();
            
            setTimeout(() => {
                const utterance = new SpeechSynthesisUtterance(text);
                
                // Set bahasa Indonesia
                utterance.lang = 'id-ID';
                
                // Gunakan suara perempuan Indonesia jika tersedia
                if (this.indonesianFemaleVoice) {
                    utterance.voice = this.indonesianFemaleVoice;
                }
                
                // Optimasi parameter untuk kejelasan suara Indonesia
                utterance.rate = 0.85; // Sedikit lambat untuk kejelasan
                utterance.pitch = 1.1; // Pitch perempuan yang natural
                utterance.volume = 1.0;
                
                // Jeda antar kalimat untuk kejelasan
                const sentences = text.split('. ');
                utterance.text = sentences.join('.  '); // Tambah spasi ekstra
                
                // Event handlers
                utterance.onstart = () => {
                    // Highlight kartu yang sedang dibaca
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.add('reading');
                    }
                };
                
                utterance.onend = () => {
                    // Hapus highlight
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.remove('reading');
                    }
                };
                
                utterance.onerror = (event) => {
                    console.error('Error membaca teks:', event.error);
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.remove('reading');
                    }
                };
                
                // Mulai berbicara
                window.speechSynthesis.speak(utterance);
                
            }, 100);
        }
        
        stopAllSpeech() {
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
            }
            // Hapus semua highlight
            document.querySelectorAll('.document-card.reading').forEach(card => {
                card.classList.remove('reading');
            });
        }
        
        toggleVoice() {
            this.isVoiceEnabled = !this.isVoiceEnabled;
            
            // Update UI
            const statusText = document.getElementById('voiceStatusText');
            const toggle = document.getElementById('voiceToggle');
            
            if (statusText) {
                statusText.textContent = this.isVoiceEnabled ? 'Aktif' : 'Mati';
                statusText.classList.toggle('active', this.isVoiceEnabled);
            }
            
            if (toggle) {
                toggle.checked = this.isVoiceEnabled;
            }
            
            // Beri feedback suara jika diaktifkan
            if (this.isVoiceEnabled) {
                this.speak('Mode suara diaktifkan. Arahkan kursor ke dokumen untuk mendengarkan deskripsi.');
            } else {
                // Hentikan semua suara saat dimatikan
                this.stopAllSpeech();
            }
            
            this.saveSettings();
        }
        
        saveSettings() {
            const settings = {
                enabled: this.isVoiceEnabled
            };
            localStorage.setItem('indonesiaVoiceEnabled', JSON.stringify(settings));
        }
        
        loadSavedSettings() {
            const saved = localStorage.getItem('indonesiaVoiceEnabled');
            if (saved) {
                try {
                    const settings = JSON.parse(saved);
                    this.isVoiceEnabled = settings.enabled || false;
                    
                    // Update UI
                    const statusText = document.getElementById('voiceStatusText');
                    const toggle = document.getElementById('voiceToggle');
                    
                    if (statusText) {
                        statusText.textContent = this.isVoiceEnabled ? 'Aktif' : 'Mati';
                        statusText.classList.toggle('active', this.isVoiceEnabled);
                    }
                    
                    if (toggle) {
                        toggle.checked = this.isVoiceEnabled;
                    }
                    
                } catch (error) {
                    console.error('Error loading voice settings:', error);
                }
            }
        }
    }
    
    // SISTEM VOICE RECOGNITION INDONESIA
    class IndonesiaVoiceRecognition {
        constructor() {
            this.isListening = false;
            this.recognition = null;
            this.init();
        }
        
        init() {
            if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
                console.warn('Voice recognition tidak didukung');
                return;
            }
            
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID'; // Bahasa Indonesia
            this.recognition.interimResults = false;
            this.recognition.maxAlternatives = 1;
            this.recognition.continuous = false;
        }
        
        startListening() {
            if (!this.recognition) return false;
            
            this.recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const searchInput = document.getElementById('mainSearchInput');
                
                // Set nilai input
                searchInput.value = transcript;
                
                // Tampilkan clear button
                document.getElementById('clearSearchBtn').style.display = 'block';
                
                // Trigger input event
                searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                
                // Auto-submit setelah 1.5 detik
                setTimeout(() => {
                    document.getElementById('searchForm').submit();
                }, 1500);
            };
            
            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                this.stopListening();
            };
            
            this.recognition.onend = () => {
                this.stopListening();
            };
            
            try {
                this.recognition.start();
                this.isListening = true;
                
                // Update UI
                this.updateUI(true);
                
                return true;
            } catch (error) {
                console.error('Failed to start recognition:', error);
                return false;
            }
        }
        
        stopListening() {
            if (this.recognition && this.isListening) {
                try {
                    this.recognition.stop();
                } catch (e) {
                    // Ignore stop errors
                }
            }
            
            this.isListening = false;
            this.updateUI(false);
        }
        
        toggleListening() {
            if (this.isListening) {
                this.stopListening();
            } else {
                this.startListening();
            }
        }
        
        updateUI(isListening) {
            const voiceBtn = document.getElementById('mainVoiceBtn');
            if (!voiceBtn) return;
            
            if (isListening) {
                voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                voiceBtn.classList.add('listening');
                voiceBtn.title = 'Berhenti mendengarkan';
            } else {
                voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                voiceBtn.classList.remove('listening');
                voiceBtn.title = 'Pencarian suara';
            }
        }
    }
    
    // SISTEM DRAGGABLE VOICE PANEL
    class DraggableVoicePanel {
        constructor() {
            this.panel = document.getElementById('voicePanel');
            this.isDragging = false;
            this.init();
        }
        
        init() {
            this.setupDraggable();
            this.loadPosition();
        }
        
        setupDraggable() {
            const handle = this.panel.querySelector('.drag-handle');
            
            handle.addEventListener('mousedown', (e) => this.startDrag(e));
            document.addEventListener('mousemove', (e) => this.drag(e));
            document.addEventListener('mouseup', () => this.stopDrag());
            
            // Touch support
            handle.addEventListener('touchstart', (e) => this.startDrag(e.touches[0]));
            document.addEventListener('touchmove', (e) => this.drag(e.touches[0]));
            document.addEventListener('touchend', () => this.stopDrag());
        }
        
        startDrag(e) {
            this.isDragging = true;
            this.panel.classList.add('dragging');
            this.offsetX = e.clientX - this.panel.offsetLeft;
            this.offsetY = e.clientY - this.panel.offsetTop;
            e.preventDefault();
        }
        
        drag(e) {
            if (!this.isDragging) return;
            
            const x = e.clientX - this.offsetX;
            const y = e.clientY - this.offsetY;
            
            // Boundary check
            const maxX = window.innerWidth - this.panel.offsetWidth;
            const maxY = window.innerHeight - this.panel.offsetHeight;
            
            this.currentX = Math.max(0, Math.min(x, maxX));
            this.currentY = Math.max(0, Math.min(y, maxY));
            
            this.panel.style.left = this.currentX + 'px';
            this.panel.style.top = this.currentY + 'px';
        }
        
        stopDrag() {
            if (this.isDragging) {
                this.isDragging = false;
                this.panel.classList.remove('dragging');
                this.savePosition();
            }
        }
        
        savePosition() {
            const position = {
                x: this.currentX,
                y: this.currentY
            };
            localStorage.setItem('voicePanelPosition', JSON.stringify(position));
        }
        
        loadPosition() {
            const saved = localStorage.getItem('voicePanelPosition');
            if (saved) {
                try {
                    const pos = JSON.parse(saved);
                    this.panel.style.left = pos.x + 'px';
                    this.panel.style.top = pos.y + 'px';
                    this.currentX = pos.x;
                    this.currentY = pos.y;
                } catch (error) {
                    console.error('Error loading panel position:', error);
                }
            }
        }
    }
    
    // SISTEM ABSTRAK
    class AbstractSystem {
        constructor() {
            this.modal = document.getElementById('abstractModal');
            this.init();
        }
        
        init() {
            this.setupEventListeners();
        }
        
        setupEventListeners() {
            document.getElementById('closeAbstractModal').addEventListener('click', () => {
                this.closeModal();
            });
            
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });
        }
        
        async openAbstract(docId) {
            try {
                const modal = document.getElementById('abstractModal');
                const modalContent = document.getElementById('abstractModalContent');
                const modalTitle = document.getElementById('abstractModalTitle');
                
                // Tampilkan loading
                modalTitle.textContent = 'Memuat Abstract...';
                modalContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
                        <p class="text-gray-600">Memuat abstract dokumen...</p>
                    </div>
                `;
                
                modal.classList.add('show');
                
                // Ambil data dari API
                const response = await fetch(`{{ url('/') }}/layanan-disabilitas/${docId}/abstract`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) throw new Error('Gagal mengambil abstract');
                
                const data = await response.json();
                
                if (data.success) {
                    // Cari dokumen di DOM
                    const documentCard = document.querySelector(`.document-card[data-doc-id="${docId}"]`);
                    const title = documentCard?.querySelector('.document-title')?.textContent || 'Dokumen';
                    const type = documentCard?.querySelector('.document-type')?.textContent || 'Dokumen';
                    
                    modalTitle.textContent = `Abstract: ${title}`;
                    modalContent.innerHTML = this.generateAbstractContent({
                        id: docId,
                        title: title,
                        type: type,
                        abstract: data.abstract
                    });
                    
                    // Setup audio button untuk membaca abstract
                    setTimeout(() => {
                        const audioBtn = document.getElementById('playAbstractAudio');
                        if (audioBtn && window.voiceSystem?.isVoiceEnabled) {
                            audioBtn.addEventListener('click', () => {
                                const abstractText = data.abstract || 'Tidak ada abstract tersedia';
                                const audioText = `Abstract dokumen ${title}. ${abstractText}`;
                                window.voiceSystem.speak(audioText);
                            });
                        }
                    }, 100);
                    
                } else {
                    throw new Error('Abstract tidak ditemukan');
                }
                
            } catch (error) {
                console.error('Error loading abstract:', error);
                document.getElementById('abstractModalContent').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                        <p class="text-gray-600">Gagal memuat abstract. Silakan coba lagi.</p>
                        <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                    </div>
                `;
            }
        }
        
        generateAbstractContent(doc) {
            const cleanAbstract = doc.abstract || 'Tidak ada abstract tersedia untuk dokumen ini.';
            
            return `
                <div class="mb-6">
                    <h4 class="font-semibold text-lg mb-2 text-blue-600">Ringkasan:</h4>
                    <div class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">
                        ${cleanAbstract}
                    </div>
                </div>
                
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Jenis:</span>
                        <span class="ml-2 font-semibold">${doc.type}</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Judul:</span>
                        <span class="ml-2 font-semibold text-sm">${doc.title}</span>
                    </div>
                </div>
                
                <div class="mt-8 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-100">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-lg text-blue-800">
                            <i class="fas fa-volume-up mr-2"></i>Dengarkan Abstract:
                        </h4>
                    </div>
                    <button id="playAbstractAudio" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle text-xl"></i>
                        Putar Audio Abstract
                    </button>
                </div>
            `;
        }
        
        closeModal() {
            this.modal.classList.remove('show');
            // Hentikan suara jika sedang berbicara
            if (window.voiceSystem) {
                window.voiceSystem.stopAllSpeech();
            }
        }
    }
    
    // SISTEM PENCARIAN UTAMA
    class DocumentSearchSystem {
        constructor() {
            this.voiceSystem = new IndonesiaVoiceSystem();
            this.voiceRecognition = new IndonesiaVoiceRecognition();
            this.draggablePanel = new DraggableVoicePanel();
            this.abstractSystem = new AbstractSystem();
            
            this.init();
        }
        
        init() {
            this.setupControls();
            this.setupEventListeners();
            
            window.voiceSystem = this.voiceSystem;
            
            // Cek dan scroll ke dokumen jika perlu
            this.checkAndScrollToDocuments();
        }
        
        setupControls() {
            // Voice search button
            document.getElementById('mainVoiceBtn').addEventListener('click', () => {
                this.voiceRecognition.toggleListening();
            });
            
            // Clear search button
            document.getElementById('clearSearchBtn').addEventListener('click', () => {
                const searchInput = document.getElementById('mainSearchInput');
                searchInput.value = '';
                searchInput.focus();
                document.getElementById('clearSearchBtn').style.display = 'none';
                
                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Pencarian dihapus');
                }
            });
            
            // Search input events
            document.getElementById('mainSearchInput').addEventListener('input', () => {
                const clearBtn = document.getElementById('clearSearchBtn');
                clearBtn.style.display = document.getElementById('mainSearchInput').value ? 'block' : 'none';
            });
            
            // Filter controls
            document.getElementById('toggleFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleFilterSection();
            });
            
            document.getElementById('applyFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.applyFilters();
            });
            
            document.getElementById('resetFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.resetFilters();
            });
            
            // Search form submit
            document.getElementById('searchForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }
        
        setupEventListeners() {
            // Abstract buttons
            document.addEventListener('click', (e) => {
                if (e.target.closest('.abstract-btn')) {
                    e.preventDefault();
                    const docId = e.target.closest('.abstract-btn').getAttribute('data-doc-id');
                    this.abstractSystem.openAbstract(docId);
                }
            });
            
            // Document card clicks
            document.querySelectorAll('.document-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (!e.target.closest('.action-btn') && !e.target.closest('a')) {
                        const docId = card.getAttribute('data-doc-id');
                        window.location.href = `{{ route('frontend.disabilitas.index') }}/${docId}`;
                    }
                });
            });
        }
        
        toggleFilterSection() {
            const filterSection = document.getElementById('filterSection');
            const isHidden = filterSection.style.display === 'none' || filterSection.style.display === '';
            
            if (isHidden) {
                filterSection.style.display = 'block';
                setTimeout(() => {
                    filterSection.style.opacity = '1';
                    filterSection.style.transform = 'translateY(0)';
                }, 10);
                
                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Filter dibuka. Pilih jenis dokumen, tahun, atau nomor dokumen.');
                }
            } else {
                filterSection.style.opacity = '0';
                filterSection.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    filterSection.style.display = 'none';
                }, 300);
                
                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Filter ditutup');
                }
            }
        }
        
        applyFilters() {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);
            
            // Tambahkan nilai filter ke form data
            const filterTypes = ['jenis', 'tahun', 'nomor'];
            filterTypes.forEach(type => {
                const filterElement = document.getElementById(type + 'Filter');
                if (filterElement && filterElement.value) {
                    formData.append(type, filterElement.value);
                }
            });
            
            // Build URL dengan hash #documents
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            let url = '{{ route('frontend.disabilitas.index') }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            url += '#documents';
            
            window.location.href = url;
            
            if (this.voiceSystem.isVoiceEnabled) {
                this.voiceSystem.speak('Filter diterapkan. Menampilkan dokumen berdasarkan pilihan filter.');
            }
        }
        
        resetFilters() {
            document.getElementById('docTypeFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('docNumberFilter').value = '';
            document.getElementById('mainSearchInput').value = '';
            document.getElementById('clearSearchBtn').style.display = 'none';
            
            window.location.href = '{{ route('frontend.disabilitas.index') }}#documents';
            
            if (this.voiceSystem.isVoiceEnabled) {
                this.voiceSystem.speak('Filter direset. Menampilkan semua dokumen.');
            }
        }
        
        performSearch() {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);
            
            // Build URL dengan hash #documents
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            let url = '{{ route('frontend.disabilitas.index') }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            url += '#documents';
            
            window.location.href = url;
        }
        
        checkAndScrollToDocuments() {
            // Cek jika ada hash #documents di URL
            if (window.location.hash === '#documents') {
                setTimeout(() => {
                    const documentsSection = document.querySelector('.documents-section');
                    if (documentsSection) {
                        documentsSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Beri feedback suara jika ada dokumen
                        if (this.voiceSystem.isVoiceEnabled) {
                            const docCount = document.querySelectorAll('.document-card').length;
                            if (docCount > 0) {
                                this.voiceSystem.speak(`Ditemukan ${docCount} dokumen. Arahkan kursor ke dokumen untuk mendengarkan deskripsi.`);
                            }
                        }
                    }
                }, 500);
            }
        }
    }
    
    // INISIALISASI SISTEM SAAT HALAMAN DIMUAT
    document.addEventListener('DOMContentLoaded', function() {
        // Tunggu sampai speech synthesis siap
        if (speechSynthesis) {
            speechSynthesis.onvoiceschanged = function() {
                if (!window.documentSearch) {
                    window.documentSearch = new DocumentSearchSystem();
                }
            };
        }
        
        // Inisialisasi sistem
        window.documentSearch = new DocumentSearchSystem();
        
        // Tambahkan keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Alt+V untuk toggle voice
            if (e.altKey && e.key === 'v') {
                e.preventDefault();
                window.documentSearch.voiceSystem.toggleVoice();
            }
            
            // Alt+S untuk voice search
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                window.documentSearch.voiceRecognition.toggleListening();
            }
            
            // Escape untuk stop semua suara
            if (e.key === 'Escape') {
                if (window.documentSearch.voiceSystem) {
                    window.documentSearch.voiceSystem.stopAllSpeech();
                }
                document.getElementById('abstractModal').classList.remove('show');
            }
        });
        
    });
</script>
@endpush
