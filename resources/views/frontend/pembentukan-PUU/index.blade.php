{{-- resources/views/frontend/pembentukan-puu/index.blade.php --}}
@extends('components.layouts.frontend')

@section('title', 'Pembentukan PUU - JDIH Kota Kendari')
@section('description', 'Dokumen Pembentukan Peraturan Perundang-undangan Kota Kendari')

@push('styles')
<style>
    /* Warna tema untuk halaman ini */
    .bg-gradient-purple-blue {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 25%, #8b5cf6 50%, #a855f7 75%, #d946ef 100%);
    }
    
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8fafc 0%, #f5f3ff 100%);
    }
    
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.1);
    }
    
    .category-active {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(34, 197, 94, 0.1) 100%);
        border: 2px solid #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
    
    .text-gradient {
        background: linear-gradient(90deg, #4f46e5, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Modal filter */
    .filter-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        width: 90%;
        max-width: 400px;
        max-height: 80vh;
        overflow-y: auto;
        display: none;
    }
    
    .filter-modal.show {
        display: block;
        animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -60%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    .modal-overlay.show {
        display: block;
        animation: overlayFadeIn 0.3s ease;
    }
    
    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Style untuk tombol clear search */
    .search-wrapper {
        position: relative;
    }
    
    .search-wrapper .clear-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #9ca3af;
        padding: 5px;
    }
    
    .search-wrapper .clear-btn:hover {
        color: #ef4444;
    }
</style>
@endpush

@section('content')
<!-- Overlay untuk modal filter -->
<div id="filterOverlay" class="modal-overlay"></div>

<!-- Modal Filter Kecil -->
<div id="filterModal" class="filter-modal">
    <div class="p-5">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-indigo-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-filter text-indigo-600 mr-2"></i>
                Filter Lanjutan
            </h3>
            <button id="closeFilterModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="filterForm" method="GET" action="{{ route('frontend.pembentukan-puu.kategori', $selectedCategory['value'] ?? 'naskah-akademik') }}">
            <!-- Filter Kategori -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-folder text-indigo-600 mr-1"></i>
                    Kategori
                </label>
                <select name="filter_kategori" class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category['value'] }}" {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 'selected' : '' }}>
                            {{ $category['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Tahun -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-indigo-600 mr-1"></i>
                    Tahun
                </label>
                <select name="filter_tahun" class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= 2000; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            
            <!-- Filter Nomor -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-hashtag text-indigo-600 mr-1"></i>
                    Nomor Dokumen
                </label>
                <input 
                    type="text" 
                    name="filter_nomor"
                    placeholder="Contoh: 5, 12, 8"
                    class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                >
            </div>
            
            <!-- Tombol Aksi -->
            <div class="flex gap-2">
                <button type="button" id="resetFilterBtn" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-redo mr-1"></i> Reset
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-medium px-4 py-2 rounded-lg transition-all duration-200">
                    <i class="fas fa-check mr-1"></i> Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hero Section -->
<section class="pt-20 pb-12 bg-gradient-light">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <!-- Icon Timbangan Hukum -->
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-purple-blue mb-4 mt-2">
                <i class="fas fa-balance-scale text-2xl text-white"></i>
            </div>
            
            <!-- Judul Besar PEMBENTUKAN PUU -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                <span class="text-gradient">PEMBENTUKAN PUU</span>
            </h1>
            
            <!-- Subjudul -->
            <p class="text-gray-600 text-lg max-w-3xl mx-auto mb-6 whitespace-nowrap overflow-hidden text-ellipsis">
                Kumpulan dokumen terkait pembentukan Peraturan Perundang-undangan Kota Kendari
            </p>
            
            <!-- FORM PENCARIAN -->
            <div class="max-w-3xl mx-auto bg-gradient-to-r from-indigo-500/70 via-purple-500/70 to-blue-500/30 rounded-xl p-5 shadow-lg border-2 border-indigo-300 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">
                    <i class="fas fa-search text-indigo-600 mr-2"></i>
                    Cari Dokumen Pembentukan PUU
                </h3>
                
                <!-- Form Pencarian Utama -->
                <form id="searchForm" action="{{ route('frontend.pembentukan-puu.kategori', $selectedCategory['value'] ?? 'naskah-akademik') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <!-- Input Pencarian -->
                    <div class="flex-1 relative">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-indigo-500">
                            <i class="fas fa-search"></i>
                        </div>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $searchQuery ?? '' }}"
                            placeholder="Cari berdasarkan judul, tahun, penulis, atau kata kunci..." 
                            class="w-full pl-10 pr-4 py-3 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white/90 transition-all duration-200"
                        >
                    </div>
                    
                    <!-- TOMBOL CARI - WARNA HIJAU -->
                    <button 
                        type="submit" 
                        class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-medium px-6 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-search"></i>
                        <span>Cari Dokumen</span>
                    </button>
                </form>
                
                <!-- Tombol Filter Kecil di Bawah Pencarian -->
                <div class="mt-4 pt-3 border-t border-indigo-300/30">
                    <button id="openFilterBtn" class="text-bg text-indigo-600 hover:text-indigo-800 flex items-center justify-center gap-1 mx-auto">
                        <i class="fas fa-sliders-h"></i>
                        <span>Filter Lanjutan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="pb-12 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Category Selection -->
        <div class="mb-10 -mt-4">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pilih Kategori Dokumen</h2>
                <p class="text-gray-600 text-sm">Klik kategori untuk langsung melihat dokumen</p>
            </div>
            
            <!-- Category Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                @foreach($categories as $category)
                    @php
                        $docCount = $allDocuments[$category['value']] ?? 0;
                    @endphp
                    <a href="{{ route('frontend.pembentukan-puu.kategori', $category['value']) }}#documents-section"
                       class="bg-gradient-to-br from-indigo-500/15 via-purple-500/15 to-blue-500/15 rounded-lg p-3 text-center hover-lift border-2 border-indigo-200 transition-all duration-200
                              {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 'category-active shadow-lg' : 'hover:border-indigo-300 hover:from-indigo-500/20 hover:via-purple-500/20 hover:to-blue-500/20' }}">
                        <div class="flex flex-col items-center">
                            <!-- Icon Lingkaran -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2
                                {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 
                                   'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg ring-2 ring-emerald-200' : 
                                   'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-md' }}">
                                @switch($category['value'])
                                    @case('naskah-akademik')
                                        <i class="fas fa-book text-sm"></i>
                                        @break
                                    @case('rancangan-puu')
                                        <i class="fas fa-file-contract text-sm"></i>
                                        @break
                                    @case('penelitian-hukum')
                                        <i class="fas fa-search text-sm"></i>
                                        @break
                                    @case('pengkajian-hukum')
                                        <i class="fas fa-gavel text-sm"></i>
                                        @break
                                    @case('pengkajian-konstitusi')
                                        <i class="fas fa-balance-scale-left text-sm"></i>
                                        @break
                                    @case('analisis-evaluasi')
                                        <i class="fas fa-chart-line text-sm"></i>
                                        @break
                                @endswitch
                            </div>
                            <!-- Nama Kategori -->
                            <div class="font-medium text-sm {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 'text-emerald-800 font-semibold' : 'text-gray-800' }} mb-1">
                                {{ $category['label'] }}
                            </div>
                            <!-- Jumlah Dokumen -->
                            <div class="text-xs font-medium {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 'text-emerald-700' : 'text-indigo-700' }}">
                                {{ $docCount }} dokumen
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <!-- Category Dropdown (Mobile) -->
            <div class="lg:hidden mt-4">
                <select id="mobileCategorySelect" 
                        class="w-full bg-indigo-50 border-2 border-indigo-200 rounded-lg px-4 py-2 text-gray-700 
                               focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        onchange="if(this.value) window.location.href=this.value+'#documents-section'">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ route('frontend.pembentukan-puu.kategori', $category['value']) }}"
                                {{ ($selectedCategory['value'] ?? '') === $category['value'] ? 'selected' : '' }}>
                            {{ $category['label'] }} ({{ $allDocuments[$category['value']] ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Documents Section - ID UNTUK SCROLL -->
        <div id="documents-section" class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-indigo-200">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-blue-500/10 px-6 py-4 border-b-2 border-indigo-300">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                {{ $selectedCategory['label'] ?? 'Dokumen' }}
                            </span>
                        </h2>
                        <p class="text-gray-700 text-sm">
                            {{ $categoryDescriptions[$selectedCategory['value'] ?? 'naskah-akademik'] ?? 'Dokumen terkait kategori ini' }}
                        </p>
                    </div>
                    <div class="text-sm text-indigo-700 bg-white/90 px-3 py-1 rounded-lg border-2 border-indigo-300 shadow-sm">
                        <i class="fas fa-file-alt mr-1"></i>
                        @if(isset($pagination))
                            {{ $pagination->total() }} dokumen
                        @else
                            {{ count($documents) }} dokumen
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Documents List -->
            <div class="p-4">
                @if(count($documents) > 0)
                    <div class="space-y-3">
                        @foreach($documents as $doc)
                            <div class="bg-white rounded-lg p-4 hover-lift border-2 border-indigo-100 hover:border-indigo-300 transition-all duration-200">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <!-- Badge dan Tahun -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 px-2 py-1 rounded-lg text-xs font-medium border-2 border-emerald-200">
                                                {{ $selectedCategory['label'] ?? 'Kategori' }}
                                            </span>
                                            <span class="text-gray-500 text-xs">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $doc['year'] ?? '-' }}
                                            </span>
                                            @if($doc['institution'] ?? false)
                                            <span class="text-gray-500 text-xs">
                                                <i class="fas fa-university mr-1"></i>
                                                {{ Illuminate\Support\Str::limit($doc['institution'], 20) }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Judul Dokumen -->
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $doc['title'] ?? 'Judul tidak tersedia' }}
                                        </h3>
                                        
                                        @if(isset($doc['description']))
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                                {{ $doc['description'] }}
                                            </p>
                                        @endif
                                        
                                        <!-- Metadata -->
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                            @if(isset($doc['author']))
                                                <span class="flex items-center">
                                                    <i class="far fa-user mr-1"></i>
                                                    {{ Illuminate\Support\Str::limit($doc['author'], 25) }}
                                                </span>
                                            @endif
                                            @if(isset($doc['pages']))
                                                <span class="flex items-center">
                                                    <i class="far fa-file mr-1"></i>
                                                    {{ $doc['pages'] }} hlm
                                                </span>
                                            @endif
                                            
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-upload mr-1"></i>
                                                @if(isset($doc['tanggal_unggah']) && $doc['tanggal_unggah'])
                                                    {{ $doc['tanggal_unggah'] }}
                                                @elseif(isset($doc['upload_date']) && $doc['upload_date'])
                                                    {{ $doc['upload_date'] }}
                                                @elseif(isset($doc['created_at']) && $doc['created_at'])
                                                    {{ $doc['created_at'] }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex flex-row md:flex-col gap-2 min-w-[140px]">
                                        <!-- Tombol Detail -->
                                        <a href="{{ $doc['detail_url'] ?? '#' }}" 
                                           class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-3 py-2 
                                                  rounded-lg text-sm text-center transition-all duration-200 flex items-center justify-center gap-1 shadow-md hover:shadow-lg">
                                            <i class="far fa-eye text-xs"></i>
                                            <span>Detail</span>
                                        </a>
                                        <!-- Tombol Download -->
                                        @if($doc['has_pdf'] ?? false)
                                        <a href="{{ $doc['download_url'] ?? '#' }}" 
                                           class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-3 py-2 
                                                  rounded-lg text-sm text-center transition-all duration-200 flex items-center justify-center gap-1 shadow-md hover:shadow-lg">
                                            <i class="fas fa-download text-xs"></i>
                                            <span>Download</span>
                                        </a>
                                        @else
                                        <button 
                                           class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white px-3 py-2 
                                                  rounded-lg text-sm text-center flex items-center justify-center gap-1 shadow-md cursor-not-allowed"
                                           disabled>
                                            <i class="fas fa-download text-xs"></i>
                                            <span>Tidak ada file</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if(isset($pagination) && $pagination->hasPages())
                    <div class="mt-6 flex justify-center">
                        <div class="flex items-center gap-1">
                            {{-- Previous Page Link --}}
                            @if($pagination->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-400">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <a href="{{ $pagination->previousPageUrl() }}#documents-section" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-600 hover:bg-indigo-50">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $pagination->currentPage();
                                $lastPage = $pagination->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp
                            
                            @if($startPage > 1)
                                <a href="{{ $pagination->url(1) }}#documents-section" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-600 hover:bg-indigo-50 text-sm">
                                    1
                                </a>
                                @if($startPage > 2)
                                    <span class="px-1 text-gray-400">...</span>
                                @endif
                            @endif
                            
                            @for($page = $startPage; $page <= $endPage; $page++)
                                @if($page == $currentPage)
                                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $pagination->url($page) }}#documents-section" 
                                       class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-600 hover:bg-indigo-50 text-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor
                            
                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <span class="px-1 text-gray-400">...</span>
                                @endif
                                <a href="{{ $pagination->url($lastPage) }}#documents-section" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-600 hover:bg-indigo-50 text-sm">
                                    {{ $lastPage }}
                                </a>
                            @endif

                            {{-- Next Page Link --}}
                            @if($pagination->hasMorePages())
                                <a href="{{ $pagination->nextPageUrl() }}#documents-section" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-600 hover:bg-indigo-50">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-indigo-200 text-gray-400">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-10">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-alt text-xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            @if(!empty($searchQuery))
                                Tidak ditemukan dokumen untuk pencarian "{{ $searchQuery }}"
                            @else
                                Belum ada dokumen
                            @endif
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 max-w-md mx-auto">
                            @if(!empty($searchQuery))
                                Coba gunakan kata kunci lain atau <a href="{{ route('frontend.pembentukan-puu.kategori', $selectedCategory['value'] ?? 'naskah-akademik') }}" class="text-indigo-600 hover:underline">lihat semua dokumen kategori ini</a>
                            @else
                                Tidak ada dokumen untuk kategori 
                                <span class="font-medium text-indigo-600">
                                    {{ $selectedCategory['label'] ?? 'ini' }}
                                </span> saat ini.
                            @endif
                        </p>
                        <a href="{{ route('frontend.pembentukan-puu.index') }}" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg">
                            <i class="fas fa-home"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pembentukan PUU page loaded');
    
    // Mobile category select
    const mobileCategorySelect = document.getElementById('mobileCategorySelect');
    if (mobileCategorySelect) {
        mobileCategorySelect.addEventListener('change', function() {
            if (this.value) {
                window.location.href = this.value + '#documents-section';
            }
        });
    }
    
    // Scroll ke dokumen jika ada hash #documents-section
    if (window.location.hash === '#documents-section') {
        setTimeout(function() {
            const element = document.getElementById('documents-section');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Clear search query when clicking X button
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        // Add clear button
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 hidden';
        clearButton.innerHTML = '<i class="fas fa-times"></i>';
        clearButton.style.display = searchInput.value ? 'block' : 'none';
        
        searchInput.parentNode.appendChild(clearButton);
        
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            clearButton.style.display = 'none';
            searchInput.focus();
        });
        
        searchInput.addEventListener('input', function() {
            clearButton.style.display = this.value ? 'block' : 'none';
        });
    }
    
    // Form Pencarian - tambahkan hash #documents-section
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Tambahkan hash #documents-section sebelum submit
            const currentAction = this.getAttribute('action');
            if (!currentAction.includes('#documents-section')) {
                this.setAttribute('action', currentAction + '#documents-section');
            }
        });
    }
    
    // Modal Filter Logic
    const filterModal = document.getElementById('filterModal');
    const filterOverlay = document.getElementById('filterOverlay');
    const openFilterBtn = document.getElementById('openFilterBtn');
    const closeFilterModal = document.getElementById('closeFilterModal');
    const filterForm = document.getElementById('filterForm');
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    
    // Buka modal filter
    if (openFilterBtn) {
        openFilterBtn.addEventListener('click', function() {
            filterModal.classList.add('show');
            filterOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Tutup modal filter
    function closeFilter() {
        filterModal.classList.remove('show');
        filterOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (closeFilterModal) {
        closeFilterModal.addEventListener('click', closeFilter);
    }
    
    if (filterOverlay) {
        filterOverlay.addEventListener('click', closeFilter);
    }
    
    // Form Filter - Update URL untuk mengarah ke kategori yang dipilih
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const kategoriSelect = this.querySelector('select[name="filter_kategori"]');
            const selectedKategori = kategoriSelect ? kategoriSelect.value : '{{ $selectedCategory["value"] ?? "naskah-akademik" }}';
            
            // Jika kategori dipilih, ubah ke route kategori tersebut
            if (selectedKategori) {
                const baseUrl = '{{ route("frontend.pembentukan-puu.kategori", ":kategori") }}'.replace(':kategori', selectedKategori);
                
                // Buat form baru dengan URL yang benar
                const formData = new FormData(this);
                const params = new URLSearchParams();
                
                // Tambahkan semua parameter filter
                for (let pair of formData.entries()) {
                    if (pair[1]) {
                        params.append(pair[0], pair[1]);
                    }
                }
                
                // Tambahkan parameter pencarian jika ada
                const searchQ = document.querySelector('input[name="q"]').value;
                if (searchQ) {
                    params.append('q', searchQ);
                }
                
                // Bangun URL akhir dengan hash #documents-section
                let finalUrl = baseUrl;
                if (params.toString()) {
                    finalUrl += '?' + params.toString();
                }
                finalUrl += '#documents-section';
                
                // Redirect ke URL yang benar
                window.location.href = finalUrl;
            } else {
                // Submit form biasa
                const currentAction = this.getAttribute('action');
                if (!currentAction.includes('#documents-section')) {
                    this.setAttribute('action', currentAction + '#documents-section');
                }
                this.submit();
            }
        });
    }
    
    // Reset filter
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            const inputs = filterForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
        });
    }
    
    // Escape key untuk tutup modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && filterModal.classList.contains('show')) {
            closeFilter();
        }
    });
});
</script>
@endpush
