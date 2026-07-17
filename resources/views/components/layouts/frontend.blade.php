<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

    <!-- Google font | Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">

    <!-- Fontawesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" />
    
    @vite('resources/css/app.css')
    
    <!-- Additional Styles -->
    @stack('styles')
    
    <style>
        /* ========== GLOBAL STYLES ========== */
        /* Custom scrollbar untuk aksesibilitas */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-blue-600 text-white px-4 py-2 rounded-lg">
        <i class="fas fa-arrow-right mr-2"></i>Loncat ke konten utama
    </a>
    
    <!-- Accessibility Quick Menu -->
    <div class="fixed bottom-4 left-4 z-40 flex flex-col gap-2 no-print">
        <button onclick="window.safeApp?.toggleFontSize('increase')" 
                class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors"
                title="Perbesar teks">
            <i class="fas fa-text-height"></i>
        </button>
        <button onclick="window.safeApp?.toggleFontSize('decrease')" 
                class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors"
                title="Perkecil teks">
            <i class="fas fa-text-width"></i>
        </button>
        <button onclick="window.safeApp?.toggleHighContrast()" 
                class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors"
                title="Mode kontras tinggi">
            <i class="fas fa-adjust"></i>
        </button>
        <button onclick="window.safeApp?.toggleDarkMode()" 
                class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors"
                title="Mode gelap/terang">
            <i class="fas fa-moon"></i>
        </button>
    </div>
    
    <!-- Progress bar -->
    <div class="fixed top-0 left-0 w-full h-1 bg-blue-600 z-40" 
         id="progress-bar" 
         style="transform: scaleX(0); transform-origin: left; transition: transform 0.3s ease;">
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
            class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all opacity-0 invisible z-40 no-print"
            aria-label="Kembali ke atas">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- JavaScript Libraries - LOAD DI AKHIR BODY -->
    <!-- Alpine.js untuk interaktivitas -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
            console.log('Safe App Initialized - Version 2.0');
            
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
            
            // Setup safe event listeners untuk header
            this.setupSafeHeaderListeners();
            
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
                console.log('Livewire detected, enabling compatibility mode');
                
                document.addEventListener('livewire:navigating', () => {
                    console.log('Livewire navigating - cleaning up');
                    this.cleanupBeforeNavigation();
                });
                
                document.addEventListener('livewire:navigated', () => {
                    console.log('Livewire navigated - reinitializing');
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
            // Re-initialize semua komponen
            this.setupSafeHeaderListeners();
            this.setupProgressBar();
            this.setupBackToTop();
            this.setupAiSearchModal();
            this.setupBerandaFilter();
            this.initializeCharts();
        },
        
        // ========== CHART FUNCTIONS ==========
        initializeCharts: function() {
            console.log('Initializing charts...');
            
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
                
                console.log(`Chart ${chartId} created successfully`);
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
                
                // Close modal when clicking outside
                aiSearchModal.addEventListener('click', (e) => {
                    if (e.target === aiSearchModal) {
                        this.closeAiSearchModal();
                    }
                });
                
                // Close modal with Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && aiSearchModal.classList.contains('show')) {
                        this.closeAiSearchModal();
                    }
                });
                
                console.log('AI Search Modal initialized');
            }
        },
        
        openAiSearchModal: function() {
            if (this.aiSearchModal) {
                this.aiSearchModal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Focus ke input search
                setTimeout(() => {
                    const aiSearchInput = document.getElementById('aiSearchInput');
                    if (aiSearchInput) {
                        aiSearchInput.focus();
                    }
                }, 100);
                
                console.log('AI Search Modal opened');
            }
        },
        
        closeAiSearchModal: function() {
            if (this.aiSearchModal) {
                this.aiSearchModal.classList.remove('show');
                document.body.style.overflow = '';
                console.log('AI Search Modal closed');
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
                
                console.log('Beranda filter form initialized:', form.id || 'unnamed');
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
        
        // ========== SAFE HEADER LISTENERS ==========
        setupSafeHeaderListeners: function() {
            // Setup dropdown header dengan cara yang aman
            const setupHeaderDropdown = () => {
                const dropdownButtons = document.querySelectorAll('header .relative.group > button');
                
                dropdownButtons.forEach(button => {
                    // Clone button untuk menghapus event listeners lama
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        
                        const dropdown = newButton.closest('.relative.group');
                        const menu = dropdown?.querySelector('.absolute');
                        
                        if (menu) {
                            const isVisible = menu.style.opacity === '1';
                            
                            // Tutup semua dropdown lain
                            document.querySelectorAll('header .absolute').forEach(otherMenu => {
                                if (otherMenu !== menu) {
                                    otherMenu.style.opacity = '0';
                                    otherMenu.style.visibility = 'hidden';
                                }
                            });
                            
                            // Toggle dropdown saat ini
                            menu.style.opacity = isVisible ? '0' : '1';
                            menu.style.visibility = isVisible ? 'hidden' : 'visible';
                            menu.style.transform = isVisible ? 'translateY(10px)' : 'translateY(0)';
                        }
                    });
                });
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('header .relative.group')) {
                        document.querySelectorAll('header .absolute').forEach(menu => {
                            menu.style.opacity = '0';
                            menu.style.visibility = 'hidden';
                            menu.style.transform = 'translateY(10px)';
                        });
                    }
                });
            };
            
            setTimeout(setupHeaderDropdown, 50);
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
                // Escape key = Close dropdowns and modals
                if (e.key === 'Escape') {
                    document.querySelectorAll('header .absolute').forEach(menu => {
                        menu.style.opacity = '0';
                        menu.style.visibility = 'hidden';
                        menu.style.transform = 'translateY(10px)';
                    });
                    
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
            
            // Load dark mode preference
            if (localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        
        toggleFontSize: function(action) {
            const html = document.documentElement;
            let currentSize = parseFloat(window.getComputedStyle(html).fontSize);
            
            if (action === 'increase') {
                currentSize = Math.min(currentSize + 2, 24);
            } else if (action === 'decrease') {
                currentSize = Math.max(currentSize - 2, 12);
            }
            
            html.style.fontSize = `${currentSize}px`;
            localStorage.setItem('fontSize', `${currentSize}px`);
            
            this.showToast(`Ukuran font diubah menjadi ${currentSize}px`, 'info');
        },
        
        toggleHighContrast: function() {
            const body = document.body;
            if (body.classList.contains('high-contrast')) {
                body.classList.remove('high-contrast');
                localStorage.setItem('highContrast', 'false');
                this.showToast('Mode kontras tinggi dimatikan');
            } else {
                body.classList.add('high-contrast');
                localStorage.setItem('highContrast', 'true');
                this.showToast('Mode kontras tinggi diaktifkan');
            }
        },
        
        toggleDarkMode: function() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                this.showToast('Mode terang diaktifkan');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                this.showToast('Mode gelap diaktifkan');
            }
        },
        
        // ========== PRINT FUNCTIONS ==========
        setupPrintListener: function() {
            window.matchMedia('print').addListener((mql) => {
                document.querySelectorAll('.no-print').forEach(el => {
                    el.style.display = mql.matches ? 'none' : '';
                });
            });
        },
        
        // ========== UTILITY FUNCTIONS ==========
        showToast: function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'info' ? 'bg-blue-500' : 
                'bg-gray-500'
            } text-white`;
            toast.textContent = message;
            toast.setAttribute('role', 'alert');
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
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
</body>
</html>
