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

	<!-- Google font | Open Sans -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
		rel="stylesheet">

	<!-- Chart.js -->
	<script src="{{ asset('assets/frontend/js/chart.js') }}"></script>

	<!-- Aos js -->
	<link href="{{ asset('assets/frontend/css/aos.css') }}" rel="stylesheet">

	<!-- Fontawesome -->
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
		crossorigin="anonymous" />
	
	<!-- Alpine.js untuk interaktivitas -->
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

	@vite('resources/css/app.css')
	
	<!-- Additional Styles -->
	@stack('styles')
	
	<style>
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
		
		/* High contrast mode support */
		@media (prefers-contrast: high) {
			* {
				border-color: black !important;
			}
		}
		
		/* Reduce motion untuk pengguna yang prefer */
		@media (prefers-reduced-motion: reduce) {
			* {
				animation-duration: 0.01ms !important;
				animation-iteration-count: 1 !important;
				transition-duration: 0.01ms !important;
			}
		}
		
		/* ========== CSS UNTUK NESTED DROPDOWN MENU ========== */
		/* Base styles untuk semua dropdown */
		.group .absolute,
		.group\/sub .absolute {
			opacity: 0;
			visibility: hidden;
			transition: all 0.2s ease-in-out;
			transform: translateY(10px);
		}

		/* Hover state untuk parent dropdown */
		.group:hover > .absolute,
		.group\/sub:hover > .absolute {
			opacity: 1;
			visibility: visible;
			transform: translateY(0);
			pointer-events: auto;
		}

		/* Style untuk nested submenu (level 2 dan 3) */
		.group\/sub .absolute {
			left: 100% !important;
			top: 0 !important;
			margin-top: 0 !important;
			margin-left: 0 !important;
			min-width: 200px;
		}

		/* Pastikan nested menu tetap visible saat hover */
		.group\/sub:hover .absolute,
		.group\/sub .absolute:hover {
			opacity: 1 !important;
			visibility: visible !important;
			transform: translateY(0) !important;
		}

		/* Z-index yang benar untuk nested menu */
		.z-50 {
			z-index: 50;
		}

		.group\/sub .absolute {
			z-index: 60 !important;
		}

		/* Hover effect untuk menu items */
		.group\/sub button:hover,
		.group\/sub a:hover {
			background-color: rgba(255, 255, 255, 0.1) !important;
			color: #4f46e5 !important;
		}

		/* Transisi untuk Alpine.js */
		[x-cloak] { 
			display: none !important; 
		}

		/* Pastikan nested menu tidak keluar dari viewport */
		.group\/sub .absolute {
			max-height: 400px;
			overflow-y: auto;
		}

		/* Style untuk arrow nested menu */
		.group\/sub svg {
			transition: transform 0.2s ease;
		}

		.group\/sub:hover svg {
			transform: rotate(90deg);
		}

		/* Dropdown animation */
		[x-show] {
			animation: fadeIn 0.2s ease-out;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		/* Mobile nested menu */
		@media (max-width: 1024px) {
			.group\/sub .absolute {
				position: static !important;
				left: auto !important;
				top: auto !important;
				width: 100% !important;
				margin-top: 8px;
				margin-left: 0;
				padding-left: 16px;
				box-shadow: none;
				background: rgba(0, 0, 0, 0.2);
				transform: none !important;
			}
		}

		/* Untuk mencegah flash of unstyled content */
		[x-cloak] {
			display: none !important;
		}

		/* Fix gap antara parent dan child menu */
		.group\/sub {
			position: relative;
		}

		/* Gradient border untuk dropdown yang menarik */
		.group\/sub .absolute > div {
			position: relative;
			border-left: 2px solid transparent;
		}

		.group\/sub .absolute > div:hover {
			border-left-color: #4f46e5;
		}

		/* CSS khusus untuk menu "Pembentukan PUU" */
		#menu-pembentukan-puu .absolute {
			min-width: 250px !important;
			background: #1e293b !important;
			border: 1px solid #334155;
		}

		#menu-pembentukan-puu .group\/sub:hover .absolute {
			display: block !important;
		}

		/* Tambahkan ini untuk memastikan menu tetap terbuka saat hover child */
		.group\/sub .absolute,
		.group .absolute {
			transition-delay: 0.1s;
		}

		.group:hover .absolute,
		.group\/sub:hover .absolute {
			transition-delay: 0s;
		}

		/* Fix khusus untuk dropdown level 3 */
		.group\/sub .group\/sub .absolute {
			margin-top: -2px !important;
		}

		/* Pastikan tidak ada pointer-events: none yang mengganggu */
		.group\/sub > .absolute,
		.group > .absolute {
			pointer-events: auto !important;
		}

		/* Tambahkan gap kecil antara parent dan child menu */
		.group\/sub::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 10px;
			height: 100%;
			background: transparent;
			z-index: 55;
		}
	</style>
</head>

<body class="font-opensans antialiased overflow-x-hidden" x-data="{ mobileMenuOpen: false }">
	<!-- Skip to main content untuk aksesibilitas -->
	<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-blue-600 text-white px-4 py-2 rounded-lg">
		<i class="fas fa-arrow-right mr-2"></i>Loncat ke konten utama
	</a>
	
	<!-- Progress bar -->
	<div class="fixed top-0 left-0 w-full h-1 bg-blue-600 z-50" 
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
			class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all opacity-0 invisible z-40"
			aria-label="Kembali ke atas">
		<i class="fas fa-chevron-up"></i>
	</button>

	<!-- JavaScript Libraries -->
	<!-- Aos js -->
	<script src="{{ asset('assets/frontend/js/aos.js') }}"></script>
	
	<!-- SweetAlert2 (optional) -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	<!-- Chart.js -->
	@stack('charts')

	<script>
		// Inisialisasi AOS
		document.addEventListener('DOMContentLoaded', function() {
			AOS.init({
				startEvent: 'load',
				once: true,
				offset: 120,
				delay: 0,
				duration: 700,
				easing: 'ease-out-cubic',
			});
			
			// Progress bar saat scroll
			const progressBar = document.getElementById('progress-bar');
			if (progressBar) {
				window.addEventListener('scroll', function() {
					const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
					const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
					const scrolled = (winScroll / height);
					progressBar.style.transform = `scaleX(${scrolled})`;
				});
			}
			
			// Back to top button
			const backToTopButton = document.getElementById('back-to-top');
			if (backToTopButton) {
				window.addEventListener('scroll', function() {
					if (window.pageYOffset > 300) {
						backToTopButton.classList.remove('opacity-0', 'invisible');
						backToTopButton.classList.add('opacity-100', 'visible');
					} else {
						backToTopButton.classList.remove('opacity-100', 'visible');
						backToTopButton.classList.add('opacity-0', 'invisible');
					}
				});
				
				backToTopButton.addEventListener('click', function() {
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
				});
			}
			
			// Keyboard shortcuts untuk navigasi
			document.addEventListener('keydown', function(e) {
				// Alt + 1 = Beranda
				if (e.altKey && e.key === '1') {
					e.preventDefault();
					window.location.href = "{{ route('frontend.beranda') }}";
				}
				// Alt + 2 = Search focus
				if (e.altKey && e.key === '2') {
					e.preventDefault();
					const searchInput = document.querySelector('input[type="search"], input[name="search"]');
					if (searchInput) {
						searchInput.focus();
					}
				}
				// Alt + 3 = Skip to main content
				if (e.altKey && e.key === '3') {
					e.preventDefault();
					document.getElementById('main-content').focus();
				}
			});
			
			// Auto-hide header on scroll (optional)
			let lastScrollTop = 0;
			const header = document.querySelector('header');
			if (header) {
				window.addEventListener('scroll', function() {
					let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
					if (scrollTop > lastScrollTop && scrollTop > 100) {
						// Scroll down
						header.style.transform = 'translateY(-100%)';
					} else {
						// Scroll up
						header.style.transform = 'translateY(0)';
					}
					header.style.transition = 'transform 0.3s ease';
					lastScrollTop = scrollTop;
				});
			}
			
			// Print stylesheet
			const printMediaQuery = window.matchMedia('print');
			printMediaQuery.addListener(function(mql) {
				if (mql.matches) {
					// Hide unnecessary elements when printing
					document.querySelectorAll('nav, header, footer, .no-print').forEach(el => {
						el.classList.add('hidden');
					});
				} else {
					document.querySelectorAll('nav, header, footer, .no-print').forEach(el => {
						el.classList.remove('hidden');
					});
				}
			});
			
			// Service Worker untuk PWA (optional)
			if ('serviceWorker' in navigator && window.location.hostname !== 'localhost') {
				navigator.serviceWorker.register('/sw.js')
					.then(registration => {
						console.log('ServiceWorker registered:', registration.scope);
					})
					.catch(error => {
						console.log('ServiceWorker registration failed:', error);
					});
			}
			
			// Load time measurement
			window.addEventListener('load', function() {
				const loadTime = window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart;
				console.log('Page load time: ' + loadTime + 'ms');
				
				// Send to analytics if needed
				if (loadTime > 3000) {
					console.warn('Page load time is slow: ' + loadTime + 'ms');
				}
			});
			
			// Fix untuk nested dropdown menu
			initNestedDropdowns();
			
			// Debug function untuk memastikan menu berfungsi
			debugNestedDropdowns();
		});
		
		// Global utility functions
		function showToast(message, type = 'success') {
			const toast = document.createElement('div');
			toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
				type === 'success' ? 'bg-green-500' : 
				type === 'error' ? 'bg-red-500' : 
				'bg-blue-500'
			} text-white`;
			toast.textContent = message;
			toast.setAttribute('role', 'alert');
			
			document.body.appendChild(toast);
			
			setTimeout(() => {
				toast.style.opacity = '0';
				toast.style.transition = 'opacity 0.5s ease';
				setTimeout(() => {
					document.body.removeChild(toast);
				}, 500);
			}, 3000);
		}
		
		function copyToClipboard(text) {
			navigator.clipboard.writeText(text)
				.then(() => showToast('Teks disalin ke clipboard!', 'success'))
				.catch(err => showToast('Gagal menyalin teks', 'error'));
		}
		
		function toggleDarkMode() {
			const html = document.documentElement;
			if (html.classList.contains('dark')) {
				html.classList.remove('dark');
				localStorage.setItem('theme', 'light');
				showToast('Mode terang diaktifkan');
			} else {
				html.classList.add('dark');
				localStorage.setItem('theme', 'dark');
				showToast('Mode gelap diaktifkan');
			}
		}
		
		// Fungsi khusus untuk nested dropdown - VERSI DIPERBAIKI
		function initNestedDropdowns() {
			console.log('Initializing nested dropdowns...');
			
			// Tambahkan event listener untuk semua dropdown
			const allDropdowns = document.querySelectorAll('.group, .group\\/sub');
			
			allDropdowns.forEach(dropdown => {
				// Hapus event listeners lama jika ada
				dropdown.removeEventListener('mouseenter', handleMouseEnter);
				dropdown.removeEventListener('mouseleave', handleMouseLeave);
				
				// Tambahkan event listeners baru
				dropdown.addEventListener('mouseenter', handleMouseEnter);
				dropdown.addEventListener('mouseleave', handleMouseLeave);
			});
			
			// Tambahkan click outside untuk menutup semua dropdown
			document.addEventListener('click', function(e) {
				if (!e.target.closest('.group, .group\\/sub')) {
					const openDropdowns = document.querySelectorAll('.group .absolute, .group\\/sub .absolute');
					openDropdowns.forEach(dropdown => {
						dropdown.style.opacity = '0';
						dropdown.style.visibility = 'hidden';
						dropdown.style.transform = 'translateY(10px)';
					});
				}
			});
			
			// Event handler functions
			function handleMouseEnter(e) {
				const dropdown = e.currentTarget;
				const nestedMenu = dropdown.querySelector('.absolute');
				
				if (nestedMenu) {
					// Close other dropdowns at the same level
					const siblings = dropdown.parentElement.querySelectorAll('.group, .group\\/sub');
					siblings.forEach(sibling => {
						if (sibling !== dropdown) {
							const siblingMenu = sibling.querySelector('.absolute');
							if (siblingMenu) {
								siblingMenu.style.opacity = '0';
								siblingMenu.style.visibility = 'hidden';
								siblingMenu.style.transform = 'translateY(10px)';
							}
						}
					});
					
					// Open this dropdown
					nestedMenu.style.opacity = '1';
					nestedMenu.style.visibility = 'visible';
					nestedMenu.style.transform = 'translateY(0)';
					
					console.log('Opened dropdown for:', dropdown.id || dropdown.className);
				}
			}
			
			function handleMouseLeave(e) {
				const dropdown = e.currentTarget;
				const nestedMenu = dropdown.querySelector('.absolute');
				const relatedTarget = e.relatedTarget;
				
				// Check if mouse is moving to a child element
				if (nestedMenu && relatedTarget && !nestedMenu.contains(relatedTarget)) {
					nestedMenu.style.opacity = '0';
					nestedMenu.style.visibility = 'hidden';
					nestedMenu.style.transform = 'translateY(10px)';
				}
			}
		}
		
		// Debug function untuk nested dropdowns
		function debugNestedDropdowns() {
			// Cek apakah dalam development mode
			if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
				console.log('=== DEBUG NESTED DROPDOWNS ===');
				
				// Cek semua dropdown
				const dropdowns = document.querySelectorAll('.group, .group\\/sub');
				console.log(`Found ${dropdowns.length} dropdown elements`);
				
				// Cek menu "Pembentukan PUU" khususnya
				const menuPembentukan = document.getElementById('menu-pembentukan-puu');
				if (menuPembentukan) {
					console.log('Menu "Pembentukan PUU" found:', menuPembentukan);
					
					const subMenu = menuPembentukan.querySelector('.absolute');
					if (subMenu) {
						console.log('Submenu found with', subMenu.children.length, 'children');
						
						// Cek sub-submenu jika ada
						const subSubMenus = subMenu.querySelectorAll('.group\\/sub');
						console.log('Found', subSubMenus.length, 'nested dropdowns inside');
						
						subSubMenus.forEach((subSub, index) => {
							const nestedMenu = subSub.querySelector('.absolute');
							if (nestedMenu) {
								console.log(`Nested menu ${index + 1}:`, nestedMenu.children.length, 'items');
							}
						});
					}
				}
				
				console.log('=== END DEBUG ===');
			}
		}
		
		// Check saved theme preference
		if (localStorage.getItem('theme') === 'dark' || 
			(!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
			document.documentElement.classList.add('dark');
		} else {
			document.documentElement.classList.remove('dark');
		}
		
		// Debug function untuk cek menu structure dari config
		function debugMenuStructure() {
			console.log('=== DEBUG MENU STRUCTURE ===');
			const menus = @json(config('app.menus'));
			const infoHukumMenu = menus.find(menu => menu.label === 'Informasi Hukum');
			if (infoHukumMenu && infoHukumMenu.sub) {
				console.log('Informasi Hukum sub menus:', infoHukumMenu.sub);
				const pembentukanPuu = infoHukumMenu.sub.find(sub => sub.label === 'Pembentukan PUU');
				if (pembentukanPuu) {
					console.log('Pembentukan PUU sub-sub menus:', pembentukanPuu.sub);
				}
			}
			console.log('=== END DEBUG ===');
		}
		
		// Panggil debug pada development
		if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
			setTimeout(debugMenuStructure, 1000);
		}
		
		// Expose debug function ke global untuk testing
		window.debugMenu = debugMenuStructure;
		window.reinitDropdowns = initNestedDropdowns;
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