<section class="relative h-195 lg:h-190 flex items-center justify-center text-white">

    <!-- BACKGROUND IMAGE -->
    <div class="absolute inset-0" data-aos="zoom-out" data-aos-duration="1200">
        <img
            src="{{ asset('assets/img/background.jpeg') }}"
            alt="Kota Kendari"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/65"></div>
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 max-w-5xl mx-auto px-4 mt-23 text-center">
        
        <!-- TITLE -->
		<p
			class="text-sm lg:text-base tracking-widest uppercase text-slate-300 mb-0"
			data-aos="fade-down"
			data-aos-delay="100">
			Selamat Datang di Situs Resmi
		</p>

		<div
			class="w-14 h-1 bg-primary mx-auto mb-6"
			data-aos="zoom-in"
			data-aos-delay="200">
		</div>

		<!-- TITLE -->
		<h1
			class="text-2xl md:text-4xl font-bold leading-tight mb-6"
			data-aos="fade-up"
			data-aos-delay="300">
			Jaringan Dokumentasi dan Informasi Hukum <br>
			<span class="text-primary">Kota Kendari</span>
		</h1>

		<!-- QUOTE -->
		<p
			class="text-sm lg:text-base italic text-slate-200 max-w-3xl mx-auto mb-10"
			data-aos="fade-up"
			data-aos-delay="450">
			"Inae konasara ie'e pinesara inae lia" <br>
			<span class="not-italic text-slate-300">
				Siapa yang menghargai adat ia akan dihormati
			</span>
		</p>

        <!-- SEARCH BOX -->
        <div
            class="bg-black/50 backdrop-blur-md rounded-2xl p-6 md:p-8 shadow-2xl max-w-4xl mx-auto"
            data-aos="fade-up"
            data-aos-delay="600">

            <!-- FORM PENCARIAN UTAMA -->
            <form id="globalSearchForm" method="GET" action="{{ route('frontend.dokumen.index', 'peraturan') }}"
    class="flex flex-col md:flex-row gap-4">
    <div class="flex-1 relative">
        <input
            type="text"
            placeholder="Masukkan Kata Kunci Pencarian Dokumen Hukum..."
            class="bg-white text-xs lg:text-sm w-full rounded px-3 lg:px-5 py-3 text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none"
            name="q"
            id="globalSearchInput"
            value="{{ request('q') }}"
            autocomplete="off">
        
        <!-- HIDDEN INPUT UNTUK KATEGORI DEFAULT -->
        <input type="hidden" name="kategori" id="searchKategori" value="peraturan">
        
        <!-- SUGGESTION DROPDOWN -->
        <div id="searchSuggestions" class="absolute z-50 w-full bg-white mt-1 rounded-lg shadow-lg border border-gray-200 hidden max-h-60 overflow-y-auto">
            <!-- Suggestions akan diisi oleh JavaScript -->
        </div>
    </div>

    <button
        type="submit"
        class="bg-primary hover:bg-primary-hover text-white text-xs lg:text-sm font-semibold px-8 py-3 rounded flex items-center justify-center gap-2 transition">
        <i class="fas fa-search"></i>
        CARI DOKUMEN
    </button>
</form>

            <!-- TOMBOL FILTER PENCARIAN LANJUTAN -->
            <div class="mt-3 flex justify-center" data-aos="fade-up" data-aos-delay="650">
                <button 
                    type="button"
                    id="toggleFilterBtn"
                    class="bg-white/20 hover:bg-white/30 text-white text-xs lg:text-sm font-medium px-4 py-2 rounded flex items-center justify-center gap-2 transition">
                    <i class="fas fa-filter"></i>
                    FILTER PENCARIAN LANJUTAN
                </button>
            </div>

            <!-- AI SEARCH BOX -->
            <div class="mt-4" data-aos="fade-up" data-aos-delay="700">
                <div class="ai-search-box">
                    <h2 class="ai-search-title">
                        <i class="fas fa-robot"></i>
                        TANYA AI TENTANG DOKUMEN HUKUM
                    </h2>
                    
                    <div class="ai-search-wrapper">
                        <input type="text" 
                               id="aiSearchInput" 
                               class="ai-search-input" 
                               placeholder="Contoh: 'Cari peraturan tentang hukum di Kota Kendari'">
                        <button id="performAiSearch" class="ai-search-button">
                            <i class="fas fa-robot"></i>
                            Tanya AI
                        </button>
                    </div>
                </div>
            </div>
        </div>
         <!-- TOMBOL SURVEI KEPUASAN - PERSIS DI BAWAH AI SEARCH -->
            <div class="mt-2 text-center" data-aos="fade-up" data-aos-delay="750">
                <!-- Link/Button Survei -->
                <a href="javascript:void(0)" 
                   onclick="bukaModalSurveiJDIH()"
                   class="inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full md:w-auto border border-white/20">
                    <i class="fas fa-clipboard-check text-xl"></i>
                    <span class="text-base md:text-lg">ISI SURVEI KEPUASAN</span>
                    <i class="fas fa-chevron-right text-sm"></i>
                </a>
                <p class="text-blue-200 text-xs md:text-sm mt-2 flex items-center justify-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Bantu kami meningkatkan kualitas layanan JDIH dengan mengisi survei singkat
                </p>
            </div>
    </div>

</section>

<!-- MODAL SURVEI KEPUASAN JDIH -->
<div id="modalSurveiJDIH" class="fixed inset-0 bg-black/80 z-[999999] hidden items-start justify-center p-2 md:p-6 overflow-y-auto" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-8 overflow-hidden animate-modalSlideIn">  
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-6 py-5 flex justify-between items-center">
            <h3 class="text-white text-xl md:text-2xl font-bold flex items-center gap-3">
                <i class="fas fa-star text-yellow-300"></i>
                Berikan Penilaian Terbaik Anda
            </h3>
            <button onclick="tutupModalSurveiJDIH()" 
                    class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center text-2xl transition-all duration-300 hover:rotate-90">
                &times;
            </button>
        </div>
        
        <!-- Body Modal - Iframe Survei -->
        <div class="h-[500px] md:h-[600px] w-full">
            <iframe 
                src="https://surveidigital.spbe.go.id/embed/survey/eyJzdXJ2ZXlfaWQiOjIsInNlcnZpY2VfaWQiOjE3NCwiaG9zdCI6Imh0dHBzOi8vamRpaC5rZW5kYXJpa290YS5nby5pZCxodHRwOi8vamRpaGtlbmRhcmkudGVzdCIsImtleSI6Ijc3elJnSkFrIn0=/embed/view/"
                class="w-full h-full border-0"
                title="Survei Kepuasan Pengguna Layanan Digital JDIH"
                allow="fullscreen"
                loading="lazy">
            </iframe>
        </div>
        
        <!-- Footer Modal -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                <i class="fas fa-balance-scale text-blue-600 mr-2"></i>
                Survei oleh KemenPAN-RB - Layanan Digital Pemerintah
            </p>
        </div>
        
    </div>
</div>

<!-- MODAL FILTER PENCARIAN LANJUTAN -->
<div id="filterModal" class="fixed inset-0 bg-black/50 z-9999 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" data-aos="zoom-in" data-aos-duration="300">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800">
                <i class="fas fa-filter text-primary mr-2"></i>
                Filter Pencarian Lanjutan
            </h3>
            <button id="closeFilterModal" class="text-slate-500 hover:text-slate-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="advancedFilterForm">
                <!-- Filter Kategori -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                    <select 
                        id="filterKategori" 
                        name="kategori"
                        class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition">
                        <option value="">Pilih Kategori</option>
                        <option value="peraturan">Peraturan</option>
                        <option value="monografi">Monografi</option>
                        <option value="artikel">Artikel</option>
                        <option value="putusan">Putusan</option>
                        <option value="puu">Perancangan PUU</option>
                        <option value="disabilitas">Layanan Disabilitas</option>
                    </select>
                </div>
                
                <!-- Filter Tahun -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tahun</label>
                    <select 
                        id="filterTahun" 
                        name="tahun"
                        class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition">
                        <option value="">Pilih Tahun</option>
                        @php
                            $currentYear = date('Y');
                            for($year = $currentYear; $year >= 2000; $year--) {
                                echo "<option value='{$year}'>{$year}</option>";
                            }
                        @endphp
                    </select>
                </div>
                
                <!-- Filter Nomor -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Dokumen</label>
                    <input 
                        type="text" 
                        id="filterNomor" 
                        name="nomor"
                        placeholder="Masukkan nomor dokumen"
                        class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition">
                </div>
                
                <!-- Filter Status -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select 
                        id="filterStatus" 
                        name="status"
                        class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition">
                        <option value="">Pilih Status</option>
                        <option value="berlaku">Berlaku</option>
                        <option value="tidak-berlaku">Tidak Berlaku</option>
                        <option value="dicabut">Dicabut</option>
                        <option value="direvisi">Direvisi</option>
                    </select>
                </div>
                
                <!-- Tombol Aksi -->
                <div class="flex gap-3 pt-4 border-t">
                    <button 
                        type="button"
                        id="applyFilterBtn"
                        class="flex-1 bg-primary hover:bg-primary-hover text-white text-sm font-semibold px-4 py-3 rounded-lg flex items-center justify-center gap-2 transition">
                        <i class="fas fa-filter"></i>
                        TERAPKAN FILTER
                    </button>
                    <button 
                        type="button"
                        id="resetFilterBtn"
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold px-4 py-3 rounded-lg flex items-center justify-center gap-2 transition">
                        <i class="fas fa-redo"></i>
                        RESET
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HANYA TAMBAHKAN SATU BARIS INI DI BAWAH SECTION -->
@include('components.frontend.ai-search-modal')

@push('styles')
<style>
    /* AI Search Box Styling */
    .ai-search-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
    }
    
    .ai-search-title {
        color: white;
        text-align: center;
        margin-bottom: 15px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .ai-search-wrapper {
        position: relative;
        margin-bottom: 10px;
    }
    
    .ai-search-input {
        width: 100%;
        padding: 14px 120px 14px 20px;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        background: white !important;
        color: #000000 !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .ai-search-input:focus {
        outline: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        border-color: #667eea;
    }
    
    .ai-search-button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
    }
    
    .ai-search-button:hover {
        transform: translateY(-50%) scale(1.05);
        box-shadow: 0 6px 15px rgba(139, 92, 246, 0.4);
    }
    
    .ai-search-examples {
        color: rgba(255,255,255,0.9);
        font-size: 0.8rem;
        text-align: center;
        margin-top: 10px;
        font-style: italic;
    }
    
    /* Modal Survei Animation */
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .animate-modalSlideIn {
        animation: modalSlideIn 0.4s ease-out;
    }
    
    /* Modal Filter Styling */
    #filterModal {
        backdrop-filter: blur(4px);
    }
    
    #filterModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    /* Filter Form Styling */
    #advancedFilterForm select,
    #advancedFilterForm input {
        transition: all 0.3s ease;
    }
    
    #advancedFilterForm select:focus,
    #advancedFilterForm input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    #applyFilterBtn, #resetFilterBtn {
        transition: all 0.3s ease;
    }
    
    #applyFilterBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }
    
    #resetFilterBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
    }
    
    /* AI Search Modal */
    .ai-search-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .ai-search-modal.show {
        display: flex;
    }
    
    .ai-search-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        width: 90%;
        max-width: 800px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .ai-search-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .ai-search-modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .ai-results {
        margin-top: 15px;
    }
    
    .ai-result-item {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 15px;
        border-left: 4px solid #8b5cf6;
        transition: all 0.3s;
    }
    
    .ai-result-item:hover {
        background: #f1f5f9;
        border-color: #c7d2fe;
        transform: translateX(5px);
    }
    
    /* Accuracy Badge */
    .accuracy-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 10px;
    }
    
    .accuracy-badge.yellow {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .accuracy-badge.orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    /* SEARCH SUGGESTIONS STYLING */
    #searchSuggestions {
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    max-height: 300px;
    overflow-y: auto;
}

.suggestion-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
}

.suggestion-item:hover {
    background-color: #f8fafc;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.suggestion-desc {
    font-size: 0.8rem;
    color: #64748b;
}

.suggestion-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-left: 8px;
}

.badge-disabilitas {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    color: white;
}

.badge-puu {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.badge-default {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

/* Filter Form Styling */
#advancedFilterForm select,
#advancedFilterForm input {
    transition: all 0.3s ease;
}

#advancedFilterForm select:focus,
#advancedFilterForm input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

#applyFilterBtn, #resetFilterBtn {
    transition: all 0.3s ease;
}

#applyFilterBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

#resetFilterBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
}
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #globalSearchForm {
            flex-direction: column;
        }
        
        button[type="submit"] {
            width: 100%;
        }
        
        .ai-search-input {
            padding: 12px 100px 12px 15px;
            font-size: 0.9rem;
        }
        
        .ai-search-button {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        #toggleFilterBtn {
            width: 100%;
        }
        
        /* Header text responsive */
        .absolute.top-0.left-0.right-0 .text-sm {
            font-size: 0.75rem;
            text-align: center;
            padding: 0 10px;
        }
    }
    
    @media (max-width: 640px) {
        #filterModal .p-6 {
            padding: 1rem;
        }
        
        #filterModal .flex.gap-3 {
            flex-direction: column;
        }
        
        #filterModal .flex.gap-3 button {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ========== FUNGSI MODAL SURVEI JDIH ==========
    window.bukaModalSurveiJDIH = function() {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    window.tutupModalSurveiJDIH = function() {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Tutup modal dengan klik di luar
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal && e.target === modal) {
            tutupModalSurveiJDIH();
        }
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modalSurveiJDIH');
            if (modal && !modal.classList.contains('hidden')) {
                tutupModalSurveiJDIH();
            }
        }
    });

    // ========== JAVASCRIPT KHUSUS PENCARIAN DOKUMEN ==========
// Fungsi ini untuk suggestion dropdown tanpa mengganggu form submit
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // ELEMEN PENCARIAN
    const searchInput = document.getElementById('globalSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const searchForm = document.getElementById('globalSearchForm');
    
    if (!searchInput || !searchSuggestions) {
        console.log('Elemen pencarian tidak ditemukan');
        return;
    }
    
    // KATA KUNCI UNTUK SUGGESTIONS
    const keywordMap = {
        'disabilitas': { 
            name: 'Layanan Disabilitas', 
            route: '{{ route("frontend.disabilitas.index") }}', 
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'difabel': { 
            name: 'Layanan Disabilitas', 
            route: '{{ route("frontend.disabilitas.index") }}', 
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'cacat': { 
            name: 'Layanan Disabilitas', 
            route: '{{ route("frontend.disabilitas.index") }}', 
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'puu': { 
            name: 'Perancangan PUU', 
            route: '{{ route("frontend.pembentukan-puu.index") }}', 
            badge: 'badge-puu',
            desc: 'Dokumen proses perancangan Peraturan Undang-Undang'
        },
        'peraturan': { 
            name: 'Peraturan', 
            route: '{{ route("frontend.dokumen.index", "peraturan") }}', 
            badge: 'badge-default',
            desc: 'Cari di kategori peraturan perundang-undangan'
        },
        'monografi': { 
            name: 'Monografi', 
            route: '{{ route("frontend.dokumen.index", "monografi") }}', 
            badge: 'badge-default',
            desc: 'Cari di kategori monografi hukum'
        },
        'artikel': { 
            name: 'Artikel', 
            route: '{{ route("frontend.dokumen.index", "artikel") }}', 
            badge: 'badge-default',
            desc: 'Cari di kategori artikel hukum'
        },
        'putusan': { 
            name: 'Putusan', 
            route: '{{ route("frontend.dokumen.index", "putusan") }}', 
            badge: 'badge-default',
            desc: 'Cari di kategori putusan pengadilan'
        }
    };
    
    // FUNGSI HANDLE INPUT PENCARIAN
    function handleSearchInput() {
        const query = searchInput.value.toLowerCase().trim();
        
        if (!query) {
            searchSuggestions.classList.add('hidden');
            return;
        }
        
        let matchedKeywords = [];
        
        for (const [keyword, data] of Object.entries(keywordMap)) {
            if (query.includes(keyword)) {
                matchedKeywords.push({ keyword, data });
            }
        }
        
        if (matchedKeywords.length > 0) {
            let html = '';
            matchedKeywords.slice(0, 4).forEach(item => {
                html += `
                    <div class="suggestion-item" onclick="window.location.href='${item.data.route}?q=${encodeURIComponent(searchInput.value)}'">
                        <div class="suggestion-title">
                            ${item.data.name}
                            <span class="suggestion-badge ${item.data.badge}">${item.keyword}</span>
                        </div>
                        <div class="suggestion-desc">${item.data.desc}</div>
                    </div>
                `;
            });
            
            // TAMBAHKAN OPSI PENCARIAN DEFAULT
            html += `
                <div class="suggestion-item" onclick="document.getElementById('globalSearchForm').submit();">
                    <div class="suggestion-title">
                        Cari "${searchInput.value}" di Semua Dokumen
                        <span class="suggestion-badge badge-default">Default</span>
                    </div>
                    <div class="suggestion-desc">Cari dokumen dengan kata kunci "${searchInput.value}"</div>
                </div>
            `;
            
            searchSuggestions.innerHTML = html;
            searchSuggestions.classList.remove('hidden');
        } else {
            // TAMPILKAN PENCARIAN DEFAULT
            let html = `
                <div class="suggestion-item" onclick="document.getElementById('globalSearchForm').submit();">
                    <div class="suggestion-title">
                        Cari "${searchInput.value}" di Semua Dokumen
                        <span class="suggestion-badge badge-default">Default</span>
                    </div>
                    <div class="suggestion-desc">Cari dokumen dengan kata kunci "${searchInput.value}"</div>
                </div>
            `;
            searchSuggestions.innerHTML = html;
            searchSuggestions.classList.remove('hidden');
        }
    }
    
    // EVENT LISTENER UNTUK INPUT
    searchInput.removeEventListener('input', handleSearchInput);
    searchInput.addEventListener('input', handleSearchInput);
    
    // SEMBUNYIKAN SUGGESTIONS SAAT KLIK DI LUAR
    document.removeEventListener('click', function(e) {});
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            searchSuggestions.classList.add('hidden');
        }
    });
    
    // ISI ULANG NILAI SEARCH DARI URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('q');
    if (searchQuery) {
        searchInput.value = searchQuery;
    }
    
    console.log('JavaScript pencarian dokumen siap - form submit normal');
});
    // ========== FIXED FILTER MODAL SYSTEM ==========
    (function() {
        'use strict';
        
        let filterModalInitialized = false;
        
        function initializeFilterModal() {
            if (filterModalInitialized) {
                console.log('Filter modal already initialized');
                return;
            }
            
            console.log('Initializing filter modal system');
            
            const toggleFilterBtn = document.getElementById('toggleFilterBtn');
            const filterModal = document.getElementById('filterModal');
            const closeFilterModal = document.getElementById('closeFilterModal');
            
            if (!toggleFilterBtn || !filterModal || !closeFilterModal) {
                console.error('Filter modal elements not found');
                return;
            }
            
            // Clone elements untuk hapus event listeners lama
            const newToggleBtn = toggleFilterBtn.cloneNode(true);
            toggleFilterBtn.parentNode.replaceChild(newToggleBtn, toggleFilterBtn);
            
            // Buka modal ketika tombol filter diklik
            newToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Opening filter modal');
                filterModal.classList.remove('hidden');
                filterModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
            
            // Tutup modal ketika tombol close diklik
            closeFilterModal.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Closing filter modal');
                filterModal.classList.remove('flex');
                filterModal.classList.add('hidden');
                document.body.style.overflow = '';
            });
            
            // Tutup modal ketika klik di luar modal
            filterModal.addEventListener('click', function(e) {
                if (e.target === filterModal) {
                    console.log('Closing filter modal (outside click)');
                    filterModal.classList.remove('flex');
                    filterModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
            
            // Tutup modal dengan tombol ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && filterModal.classList.contains('flex')) {
                    console.log('Closing filter modal (ESC key)');
                    filterModal.classList.remove('flex');
                    filterModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
            
            // Setup advanced filters
            initializeAdvancedFilters();
            
            filterModalInitialized = true;
            console.log('Filter modal initialized successfully');
        }
        
        function initializeAdvancedFilters() {
            const applyFilterBtn = document.getElementById('applyFilterBtn');
            const resetFilterBtn = document.getElementById('resetFilterBtn');
            
            if (!applyFilterBtn || !resetFilterBtn) {
                console.error('Filter buttons not found');
                return;
            }
            
            // Clone buttons untuk hapus event listeners lama
            const newApplyBtn = applyFilterBtn.cloneNode(true);
            applyFilterBtn.parentNode.replaceChild(newApplyBtn, applyFilterBtn);
            
            const newResetBtn = resetFilterBtn.cloneNode(true);
            resetFilterBtn.parentNode.replaceChild(newResetBtn, resetFilterBtn);
            
            // Fungsi untuk terapkan filter
            newApplyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                applyAdvancedFilters();
            });
            
            // Fungsi untuk reset filter
            newResetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('advancedFilterForm').reset();
            });
            
            // Terapkan filter dengan Enter key pada input nomor
            const filterNomor = document.getElementById('filterNomor');
            if (filterNomor) {
                filterNomor.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyAdvancedFilters();
                    }
                });
            }
            
            console.log('Advanced filters initialized');
        }
        
        function applyAdvancedFilters() {
            console.log('Applying advanced filters');
            
            const kategori = document.getElementById('filterKategori')?.value || '';
            const tahun = document.getElementById('filterTahun')?.value || '';
            const nomor = document.getElementById('filterNomor')?.value || '';
            const status = document.getElementById('filterStatus')?.value || '';
            
            // Validasi minimal satu filter harus diisi
            if (!kategori && !tahun && !nomor && !status) {
                alert('Silakan pilih minimal satu filter untuk diterapkan');
                return;
            }
            
            // Tentukan URL berdasarkan kategori
            let baseUrl = '';
            const params = new URLSearchParams();
            
            // Mapping kategori ke route yang sesuai
            const routeMap = {
                'peraturan': '{{ route("frontend.dokumen.index", "peraturan") }}',
                'monografi': '{{ route("frontend.dokumen.index", "monografi") }}',
                'artikel': '{{ route("frontend.dokumen.index", "artikel") }}',
                'putusan': '{{ route("frontend.dokumen.index", "putusan") }}',
                'puu': '{{ route("frontend.pembentukan-puu.index") }}',
                'disabilitas': '{{ route("frontend.disabilitas.index") }}'
            };
            
            // Jika kategori dipilih, gunakan route khusus
            if (kategori && routeMap[kategori]) {
                baseUrl = routeMap[kategori];
            } else {
                // Default ke peraturan jika kategori tidak dipilih
                baseUrl = '{{ route("frontend.dokumen.index", "peraturan") }}';
            }
            
            // Tambahkan parameter filter lainnya
            if (tahun) params.append('tahun', tahun);
            if (nomor) params.append('nomor', nomor);
            if (status) params.append('status', status);
            
            // Tambahkan parameter pencarian dari input utama jika ada
            const searchQuery = document.getElementById('globalSearchInput')?.value || '';
            if (searchQuery) {
                params.append('q', searchQuery);
            } else if (!kategori) {
                // Jika tidak ada kata kunci dan tidak ada kategori, beri pesan
                alert('Silakan masukkan kata kunci pencarian atau pilih kategori');
                return;
            }
            
            // Tutup modal
            const filterModal = document.getElementById('filterModal');
            if (filterModal) {
                filterModal.classList.remove('flex');
                filterModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            // Redirect ke URL dengan parameter filter
            const queryString = params.toString();
            console.log('Redirecting to:', baseUrl + (queryString ? '?' + queryString : ''));
            window.location.href = baseUrl + (queryString ? '?' + queryString : '');
        }
        
        // ========== FIXED SMART SEARCH SYSTEM ==========
        
        
        // ========== INITIALIZATION ==========
        function setupSearchFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('q');
            
            if (searchQuery) {
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.value = searchQuery;
                }
            }
        }
        
        // Main initialization function
        function initializeAllSystems() {
            console.log('Initializing all search systems...');
            
            // Initialize filter modal
            initializeFilterModal();
            
           
            
            // Setup search from URL
            setupSearchFromUrl();
            
            console.log('All search systems initialized');
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAllSystems);
        } else {
            initializeAllSystems();
        }
        
        // Handle Livewire navigation
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:navigated', function() {
                console.log('Livewire navigated - reinitializing search systems');
                setTimeout(initializeAllSystems, 100);
            });
        }
        
        // Expose functions to window
        window.initializeFilterModal = initializeFilterModal;
        window.applyAdvancedFilters = applyAdvancedFilters;
        
    })(); // End IIFE
    
</script>

<!-- AI Search System Script - LOAD WITH DEFER -->
<script src="{{ asset('js/ai-search-system.js') }}" defer></script>

<!-- Initialize AI Search after DOM loaded -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AI Search akan diinisialisasi oleh file terpisah
    console.log('AI Search will be initialized by external script');
});
</script>
@endpush
