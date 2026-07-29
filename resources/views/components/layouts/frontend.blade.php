<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Tema diterapkan sebelum paint pertama agar tidak ada kedip putih (FOUC).
         Harus inline & sinkron — jangan dipindah ke file JS eksternal.

         Setelah wire:navigate, Livewire menjalankan replaceHtmlAttributes():
         atribut <html> disalin dari HTML server (class="scroll-smooth", tanpa
         "dark") sehingga tema ikut terhapus. Listener di bawah memasangnya
         kembali segera setelah swap — script <head> tidak dieksekusi ulang saat
         navigate, jadi listener ini cukup didaftarkan sekali. --}}
    <script>
        (function () {
            function terapkanTema() {
                try {
                    var t = localStorage.getItem('theme');
                    var gelap = t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', gelap);

                    // body juga diganti saat navigate → kelas kontras ikut hilang
                    if (document.body) {
                        document.body.classList.toggle('high-contrast', localStorage.getItem('highContrast') === 'true');
                    }
                } catch (e) {}
            }

            terapkanTema();
            document.addEventListener('livewire:navigated', terapkanTema);
        })();
    </script>
    <title>@yield('title', 'Situs Resmi JDIH Kota Kendari')</title>
    
    <!-- Meta Description untuk SEO -->
    <meta name="description" content="@yield('description', 'Jaringan Dokumentasi dan Informasi Hukum (JDIH) Pemerintah Kota Kendari - Akses peraturan daerah, dokumen hukum, dan informasi legal secara lengkap dan terupdate.')">
    
    <!-- CSRF Token untuk API -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'JDIH Kota Kendari')">
    <meta property="og:description" content="@yield('description', 'Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari')">
    <meta property="og:image" content="@yield('image', asset('assets/img/logo-jdih.png'))">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'JDIH Kota Kendari')">
    <meta property="twitter:description" content="@yield('description', 'Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari')">
    <meta property="twitter:image" content="@yield('image', asset('assets/img/logo-jdih.png'))">
    
    <!-- Additional Meta untuk Disabilitas -->
    <meta name="accessibility" content="WCAG 2.1 AA">
    <meta name="robots" content="index, follow">
    <meta name="rating" content="General">

    <!-- Google font | Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Fontawesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" />
    
    @vite('resources/css/app.css')

    {{-- Aset Livewire/Alpine dimuat eksplisit agar konsisten di semua halaman (termasuk non-Livewire/@extends) --}}
    @livewireStyles

    <!-- Additional Styles -->
    @stack('styles')
    
    <style>
        /* ========== GLOBAL STYLES ========== */
        [x-cloak] { display: none !important; }
        /* Scrollbar brand didefinisikan global di resources/css/app.css */

        /* Reduce motion untuk pengguna yang prefer */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* ========== ACCESSIBILITY STYLES ========== */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        .focus\:not-sr-only:focus {
            position: fixed !important;
            width: auto;
            height: auto;
            padding: 0.5rem;
            margin: 0;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }
        
        /* ========== PRINT STYLES ========== */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12pt;
            }
            
            a {
                color: black !important;
                text-decoration: none !important;
            }
        }
        
        /* ========== FIX UNTUK STATISTIK DAN CHART ========== */
        /* Pastikan container chart memiliki dimensi yang jelas */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Loading state untuk chart */
        .chart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 200px;
        }
        
        /* ========== AI SEARCH MODAL FIX ========== */
        #aiSearchModal {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        #aiSearchModal.show {
            opacity: 1;
            visibility: visible;
        }
        
        #aiSearchModal:not(.show) {
            opacity: 0;
            visibility: hidden;
        }
        
        /* ========== BERANDA FILTER FIX ========== */
        .beranda-filter form {
            pointer-events: auto !important;
        }
        
        .beranda-filter button {
            cursor: pointer !important;
        }
    </style>
</head>

<body class="font-opensans antialiased overflow-x-hidden">
    
    <!-- Skip to main content untuk aksesibilitas -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-primary text-white px-4 py-2 rounded">
        <i class="fas fa-arrow-right mr-2"></i>Loncat ke konten utama
    </a>
    
    <!-- Accessibility Quick Menu -->
    <div class="fixed bottom-4 left-4 z-50 pointer-events-auto no-print">
        {{-- Mobile: baris mendatar (hemat tinggi layar). sm+: kolom vertikal seperti semula. --}}
        <div class="flex flex-row sm:flex-col items-center gap-1 sm:gap-0.5 rounded bg-darkbg/70 p-1 shadow-xl ring-1 ring-white/10 backdrop-blur-lg">
            @include('components.frontend.language-switcher')

            <span aria-hidden="true" class="mx-0.5 h-6 w-px sm:mx-0 sm:my-0.5 sm:h-px sm:w-6 bg-white/10"></span>

            <button onclick="window.safeApp?.toggleFontSize('increase')"
                    class="flex h-9 w-9 items-center justify-center rounded text-sm text-white/80 transition hover:bg-white/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                    title="Perbesar teks" aria-label="Perbesar teks">
                <i class="fas fa-magnifying-glass-plus"></i>
            </button>
            <button onclick="window.safeApp?.toggleFontSize('decrease')"
                    class="flex h-9 w-9 items-center justify-center rounded text-sm text-white/80 transition hover:bg-white/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                    title="Perkecil teks" aria-label="Perkecil teks">
                <i class="fas fa-magnifying-glass-minus"></i>
            </button>
            <button onclick="window.safeApp?.toggleFontSize('reset')"
                    class="flex h-9 w-9 items-center justify-center rounded text-sm text-white/80 transition hover:bg-white/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                    title="Kembalikan ukuran teks normal" aria-label="Kembalikan ukuran teks normal">
                <i class="fas fa-rotate-left"></i>
            </button>

            <span aria-hidden="true" class="mx-0.5 h-6 w-px sm:mx-0 sm:my-0.5 sm:h-px sm:w-6 bg-white/10"></span>

            <button id="btnHighContrast"
                    onclick="window.safeApp?.toggleHighContrast()"
                    aria-pressed="false"
                    class="flex h-9 w-9 items-center justify-center rounded text-sm text-white/80 transition hover:bg-white/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 aria-pressed:bg-primary aria-pressed:text-white"
                    title="Mode kontras tinggi" aria-label="Mode kontras tinggi">
                <i class="fas fa-circle-half-stroke"></i>
            </button>
            <button id="btnDarkMode"
                    onclick="window.safeApp?.toggleDarkMode()"
                    aria-pressed="false"
                    class="flex h-9 w-9 items-center justify-center rounded text-sm text-white/80 transition hover:bg-white/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 aria-pressed:bg-primary aria-pressed:text-white"
                    title="Mode gelap/terang" aria-label="Mode gelap/terang">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="fixed top-0 left-0 w-full h-px bg-primary z-60"
         id="progress-bar"
         style="transform: scaleX(0); transform-origin: left; transition: transform 0.1s linear; will-change: transform;">
    </div>

    <!-- Header -->
    @include('frontend.partials.header')

    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Footer -->
    @include('frontend.partials.footer')
    
    <!-- Back to Top Button -->
    <button id="back-to-top"
            class="fixed bottom-5 right-5 sm:bottom-7 sm:right-7 z-40 flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded bg-primary text-sm text-white shadow-lg shadow-primary/30 ring-1 ring-white/10 transition-all duration-300 opacity-0 invisible no-print hover:bg-primary-hover hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
            aria-label="Kembali ke atas">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- JavaScript Libraries - LOAD DI AKHIR BODY -->
    {{-- Alpine.js TIDAK dimuat manual: sudah dibundel oleh Livewire (hindari "multiple instances of Alpine") --}}

    <!-- Chart.js untuk statistik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery (untuk datepicker dan kompatibilitas) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

    <script>
    // ========== SAFE APP OBJECT - FIXED VERSION ==========
    window.safeApp = {
        // ========== GLOBAL VARIABLES ==========
        charts: {},
        aiSearchModal: null,
        
        // ========== INITIALIZATION ==========
        init: function() {
            
            // Setup progress bar
            this.setupProgressBar();
            
            // Setup back to top button
            this.setupBackToTop();
            
            // Setup keyboard shortcuts
            this.setupKeyboardShortcuts();
            
            // Setup print listener
            this.setupPrintListener();
            
            // Load saved accessibility settings
            this.loadAccessibilitySettings();
            
            // Header dropdown kini ditangani Alpine (lihat partials/header.blade.php)

            // Setup Livewire compatibility
            this.setupLivewireCompatibility();
            
            // Setup AI Search modal
            this.setupAiSearchModal();
            
            // Setup beranda filter
            this.setupBerandaFilter();
            
            // Initialize charts jika ada
            this.initializeCharts();
        },
        
        // ========== LIVEWIRE COMPATIBILITY ==========
        setupLivewireCompatibility: function() {
            if (typeof Livewire !== 'undefined') {
                
                document.addEventListener('livewire:navigating', () => {
                    this.cleanupBeforeNavigation();
                });
                
                document.addEventListener('livewire:navigated', () => {
                    setTimeout(() => {
                        this.reinitializeAfterNavigation();
                    }, 150);
                });
            }
        },
        
        cleanupBeforeNavigation: function() {
            // Destroy charts sebelum navigasi
            this.destroyCharts();
        },
        
        reinitializeAfterNavigation: function() {
            // Kelas tema/kontras sudah dipasang ulang oleh listener di <head>
            // (tanpa jeda, agar tidak berkedip). Di sini hanya menyelaraskan
            // tombol panel aksesibilitas yang ikut terganti bersama <body>.
            this.applyHighContrast(document.body.classList.contains('high-contrast'));
            this.applyDarkMode(document.documentElement.classList.contains('dark'));

            // Re-initialize semua komponen (header dropdown ditangani Alpine)
            this.setupProgressBar();
            this.setupBackToTop();
            this.setupAiSearchModal();
            this.setupBerandaFilter();
            this.initializeCharts();
        },
        
        // ========== CHART FUNCTIONS ==========
        initializeCharts: function() {
            
            // Cari semua elemen chart
            const chartElements = document.querySelectorAll('[data-chart]');
            
            chartElements.forEach((element, index) => {
                const chartId = element.id || `chart-${index}`;
                const chartType = element.getAttribute('data-chart-type') || 'bar';
                const chartData = JSON.parse(element.getAttribute('data-chart-data') || '{}');
                const chartOptions = JSON.parse(element.getAttribute('data-chart-options') || '{}');
                
                if (chartData && Object.keys(chartData).length > 0) {
                    this.createChart(chartId, chartType, chartData, chartOptions);
                }
            });
            
            // Juga coba inisialisasi charts yang sudah ada di window
            if (window.initializeCharts && typeof window.initializeCharts === 'function') {
                setTimeout(() => {
                    window.initializeCharts();
                }, 200);
            }
        },
        
        createChart: function(chartId, type, data, options) {
            const canvas = document.getElementById(chartId);
            if (!canvas) return;
            
            // Destroy chart lama jika ada
            if (this.charts[chartId]) {
                this.charts[chartId].destroy();
            }
            
            // Default options untuk responsif
            const defaultOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };
            
            // Merge options
            const mergedOptions = { ...defaultOptions, ...options };
            
            try {
                const ctx = canvas.getContext('2d');
                this.charts[chartId] = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: mergedOptions
                });
                
            } catch (error) {
                console.error(`Error creating chart ${chartId}:`, error);
            }
        },
        
        destroyCharts: function() {
            Object.keys(this.charts).forEach(chartId => {
                if (this.charts[chartId]) {
                    this.charts[chartId].destroy();
                }
            });
            this.charts = {};
        },
        
        // ========== AI SEARCH MODAL FUNCTIONS ==========
        setupAiSearchModal: function() {
            const aiSearchModal = document.getElementById('aiSearchModal');
            const openAiSearchBtns = document.querySelectorAll('[data-ai-search-open]');
            const closeAiSearchBtn = document.getElementById('closeAiModal');
            
            if (aiSearchModal) {
                this.aiSearchModal = aiSearchModal;
                
                // Open modal handlers
                openAiSearchBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.openAiSearchModal();
                    });
                });
                
                // Close modal handler
                if (closeAiSearchBtn) {
                    closeAiSearchBtn.addEventListener('click', () => {
                        this.closeAiSearchModal();
                    });
                }
                
                // Sengaja tanpa klik-luar & Escape: percakapan mudah tertutup
                // tidak sengaja, dan isinya hilang.
            }
        },
        
        openAiSearchModal: function() {
            // dikelola ai-search-system.js (fokus ke input di dalam modal)
            if (window.openAiSearchModal) {
                window.openAiSearchModal();
            } else if (this.aiSearchModal) {
                this.aiSearchModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        },
        
        closeAiSearchModal: function() {
            if (this.aiSearchModal) {
                this.aiSearchModal.classList.remove('show');
                document.body.style.overflow = '';
            }
        },
        
        // ========== BERANDA FILTER FUNCTIONS ==========
        setupBerandaFilter: function() {
            // Cari semua form filter di beranda
            const berandaForms = document.querySelectorAll('.beranda-filter form');
            
            berandaForms.forEach(form => {
                form.classList.add('beranda-filter-active');
                
                // Setup submit handler
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitBerandaFilter(form);
                });
                
                // Setup reset handler jika ada
                const resetBtn = form.querySelector('[type="reset"], .reset-filter');
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => {
                        form.reset();
                        setTimeout(() => {
                            form.dispatchEvent(new Event('submit'));
                        }, 100);
                    });
                }
                
            });
            
            // Juga handle filter umum
            this.setupGeneralFilterHandlers();
        },
        
        submitBerandaFilter: function(form) {
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            // Add all form data to params
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            // Get current URL
            const currentUrl = new URL(window.location.href);
            
            // Preserve existing query parameters that are not in form
            const existingParams = new URLSearchParams(currentUrl.search);
            for (let [key, value] of existingParams.entries()) {
                if (!formData.has(key) && value) {
                    params.append(key, value);
                }
            }
            
            // Update URL
            currentUrl.search = params.toString();
            
            // Navigate to new URL
            window.location.href = currentUrl.toString();
        },
        
        setupGeneralFilterHandlers: function() {
            // Setup untuk semua form dengan class filter-form
            document.querySelectorAll('.filter-form:not(.beranda-filter-active)').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);
                    
                    // Preserve page parameter jika ada
                    const urlParams = new URLSearchParams(window.location.search);
                    const page = urlParams.get('page');
                    if (page && !formData.has('page')) {
                        params.append('page', page);
                    }
                    
                    const currentUrl = new URL(window.location.href);
                    currentUrl.search = params.toString();
                    
                    window.location.href = currentUrl.toString();
                });
            });
            
            // Setup untuk tombol reset filter
            document.querySelectorAll('.reset-filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    if (form) {
                        form.reset();
                        form.dispatchEvent(new Event('submit'));
                    }
                });
            });
        },
        
        // ========== PROGRESS BAR ==========
        setupProgressBar: function() {
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                const scrollHandler = () => {
                    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                    const scrolled = (winScroll / height) || 0;
                    progressBar.style.transform = `scaleX(${scrolled})`;
                };
                
                window.removeEventListener('scroll', scrollHandler);
                window.addEventListener('scroll', scrollHandler, { passive: true });
            }
        },
        
        // ========== BACK TO TOP ==========
        setupBackToTop: function() {
            const backToTopButton = document.getElementById('back-to-top');
            if (backToTopButton) {
                const scrollHandler = () => {
                    if (window.pageYOffset > 300) {
                        backToTopButton.classList.remove('opacity-0', 'invisible');
                        backToTopButton.classList.add('opacity-100', 'visible');
                    } else {
                        backToTopButton.classList.remove('opacity-100', 'visible');
                        backToTopButton.classList.add('opacity-0', 'invisible');
                    }
                };
                
                window.removeEventListener('scroll', scrollHandler);
                window.addEventListener('scroll', scrollHandler, { passive: true });
                
                backToTopButton.onclick = () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                };
            }
        },
        
        // ========== KEYBOARD SHORTCUTS ==========
        setupKeyboardShortcuts: function() {
            document.addEventListener('keydown', (e) => {
                // Alt + 1 = Beranda
                if (e.altKey && e.key === '1') {
                    e.preventDefault();
                    window.location.href = "{{ url('/') }}";
                }
                // Alt + 2 = Open AI Search
                if (e.altKey && e.key === '2') {
                    e.preventDefault();
                    this.openAiSearchModal();
                }
                // Alt + 3 = Skip to main content
                if (e.altKey && e.key === '3') {
                    e.preventDefault();
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        mainContent.focus();
                        mainContent.scrollIntoView({ behavior: 'smooth' });
                    }
                }
                // Escape key = Close modals (header dropdown ditangani Alpine)
                if (e.key === 'Escape') {
                    this.closeAiSearchModal();
                }
            });
        },
        
        // ========== ACCESSIBILITY FUNCTIONS ==========
        loadAccessibilitySettings: function() {
            // Load font size
            const fontSize = localStorage.getItem('fontSize');
            if (fontSize) {
                document.documentElement.style.fontSize = fontSize;
            }

            // Kontras tinggi: sebelumnya disimpan tapi tidak pernah dipulihkan
            this.applyHighContrast(localStorage.getItem('highContrast') === 'true');

            // Kelas .dark sudah dipasang script inline di <head> (anti-FOUC);
            // di sini hanya menyelaraskan tampilan tombolnya.
            this.applyDarkMode(document.documentElement.classList.contains('dark'));
        },
        
        toggleFontSize: function(action) {
            const html = document.documentElement;
            let currentSize = parseFloat(window.getComputedStyle(html).fontSize);
            
            if (action === 'reset') {
                html.style.removeProperty('font-size');
                localStorage.removeItem('fontSize');
                return;
            }

            if (action === 'increase') {
                currentSize = Math.min(currentSize + 2, 24);
            } else if (action === 'decrease') {
                currentSize = Math.max(currentSize - 2, 12);
            }

            html.style.fontSize = `${currentSize}px`;
            localStorage.setItem('fontSize', `${currentSize}px`);
        },
        
        toggleHighContrast: function() {
            const aktif = !document.body.classList.contains('high-contrast');
            this.applyHighContrast(aktif);
            localStorage.setItem('highContrast', aktif ? 'true' : 'false');
        },

        applyHighContrast: function(aktif) {
            document.body.classList.toggle('high-contrast', aktif);
            document.getElementById('btnHighContrast')?.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        },
        
        toggleDarkMode: function() {
            const gelap = !document.documentElement.classList.contains('dark');
            this.applyDarkMode(gelap);
            localStorage.setItem('theme', gelap ? 'dark' : 'light');
        },

        applyDarkMode: function(gelap) {
            document.documentElement.classList.toggle('dark', gelap);

            const btn = document.getElementById('btnDarkMode');
            if (!btn) return;
            btn.setAttribute('aria-pressed', gelap ? 'true' : 'false');
            btn.querySelector('i')?.setAttribute('class', gelap ? 'fas fa-sun' : 'fas fa-moon');
            btn.title = gelap ? 'Kembali ke mode terang' : 'Aktifkan mode gelap';
        },
        
        // ========== PRINT FUNCTIONS ==========
        setupPrintListener: function() {
            window.matchMedia('print').addListener((mql) => {
                document.querySelectorAll('.no-print').forEach(el => {
                    el.style.display = mql.matches ? 'none' : '';
                });
            });
        },
        
        // ========== GLOBAL EXPOSE FUNCTIONS ==========
        // Fungsi yang bisa dipanggil dari mana saja
        openAiSearch: function() {
            this.openAiSearchModal();
        },
        
        closeAiSearch: function() {
            this.closeAiSearchModal();
        },
        
        refreshCharts: function() {
            this.destroyCharts();
            setTimeout(() => {
                this.initializeCharts();
            }, 100);
        },
        
        submitFilter: function(formId) {
            const form = document.getElementById(formId);
            if (form) {
                form.dispatchEvent(new Event('submit'));
            }
        }
    };
    
    // Expose functions to window
    window.openAiSearch = () => window.safeApp?.openAiSearch();
    window.closeAiSearch = () => window.safeApp?.closeAiSearch();
    window.refreshCharts = () => window.safeApp?.refreshCharts();
    window.submitFilter = (formId) => window.safeApp?.submitFilter(formId);
    
    // Initialize app
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.safeApp.init();
        });
    } else {
        window.safeApp.init();
    }
    
    // Setup datepickers
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                language: 'id',
                todayHighlight: true
            });
        }
    });

    </script>
    
    <!-- Custom scripts per halaman -->
    @stack('scripts')
    
    <!-- Analytics (Google Analytics, Matomo, dll) -->
    @if(config('services.google_analytics.id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_analytics.id') }}');
        </script>
    @endif

    {{-- Livewire + Alpine (eksplisit): pastikan interaktivitas jalan di semua halaman, termasuk non-Livewire --}}
    @livewireScripts
</body>
</html>
