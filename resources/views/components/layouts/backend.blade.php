<!DOCTYPE html>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin - JDIH Kota Kendari</title>
	<link href="{{ asset('assets') }}/backend/5bd7bfbf/css/font-awesome.min.css" rel="stylesheet">
	<link href="{{ asset('assets') }}/backend/63e477e8/css/bootstrap.css" rel="stylesheet">
	<link href="{{ asset('assets') }}/backend/687d7f9a/css/AdminLTE.min.css" rel="stylesheet">
	<link href="{{ asset('assets') }}/backend/687d7f9a/css/skins/_all-skins.min.css" rel="stylesheet">
	<link href="{{ asset('assets') }}/backend/687d7f9a/summernote/dist/summernote.css" rel="stylesheet">

	{{-- Link href form input --}}
	@if (Str::contains(Request()->path(), ['/create', '/edit']))
		<link href="{{ asset('assets') }}/backend/85185e69/css/bootstrap-datepicker3.css" rel="stylesheet">
		<link href="{{ asset('assets') }}/backend/6a009609/css/kv-summernote.css" rel="stylesheet">
		<link href="{{ asset('assets') }}/backend/7fee4fda/css/fileinput.css" rel="stylesheet">
	@endif

	{{-- Jquery --}}
	<script src="{{ asset('assets') }}/backend/3e5a9e6a/jquery.js"></script>

	{{-- Select2 link and script --}}
	<link href="{{ asset('assets') }}/backend/select2/select2.min.css" rel="stylesheet" />
	<script src="{{ asset('assets') }}/backend/select2/select2.min.js"></script>

	<style>
		.select2-selection.select2-selection--single {
			height: 34px !important;
			opacity: 0.9 !important;
		}

		.select2-results__option:hover {
			color: white !important;
		}

		.no-spinner::-webkit-inner-spin-button,
		.no-spinner::-webkit-outer-spin-button {
			-webkit-appearance: none !important;
			margin: 0 !important;
		}

		/* Firefox */
		.no-spinner {
			-moz-appearance: textfield !important;
		}

		/* ═══════════════════════════════════════════════
		   Premium admin chrome — global, tetap skin-red
		   Hanya restyle struktur AdminLTE, tanpa ubah markup
		   ═══════════════════════════════════════════════ */

		/* Header */
		.skin-red .main-header .logo,
		.skin-red .main-header .logo:hover {
			background: #8f1c17; color: #fff;
			font-weight: 700; letter-spacing: .06em;
			border-bottom: none; transition: background .2s ease;
		}
		.skin-red .main-header .navbar {
			background: #b3241d;
			background: linear-gradient(90deg, #a51f19 0%, #c0392b 100%);
		}
		.main-header { box-shadow: 0 1px 0 rgba(0,0,0,.12), 0 3px 10px rgba(0,0,0,.06); }
		.skin-red .main-header .navbar .sidebar-toggle:hover { background: rgba(0,0,0,.14); }
		.main-header .navbar .nav > .user-menu > a { display: flex; align-items: center; gap: 9px; padding-top: 12px; padding-bottom: 12px; }
		.main-header .user-menu .user-image {
			margin: 0; width: 30px; height: 30px;
			border: 2px solid rgba(255,255,255,.55); object-fit: cover; aspect-ratio: 1/1;
		}
		.main-header .navbar-custom-menu .hidden-xs { font-weight: 600; letter-spacing: .01em; }

		.user-menu .dropdown-menu {
			border: none; padding: 0; border-radius: 9px; overflow: hidden;
			box-shadow: 0 10px 32px rgba(0,0,0,.18);
		}
		.user-menu .user-header {
			background: #b3241d;
			background: linear-gradient(135deg, #a51f19 0%, #c0392b 100%);
			height: auto; padding: 22px 15px;
		}
		.user-menu .user-header > img { border: 3px solid rgba(255,255,255,.35); }
		.user-menu .user-footer { padding: 12px; background: #faf9f7; }
		.user-menu .user-footer .btn-flat { border-radius: 6px; font-weight: 600; }

		/* Sidebar (gelap, dirapikan) */
		.skin-red .main-sidebar, .skin-red .left-side { background: #20262b; }
		/* Isi kolom sidebar sampai dasar — hilangkan celah warna di bawah menu */
		.skin-red .wrapper { background-color: #20262b; }
		.skin-red .content-wrapper { background-color: #f4f6f9; }
		.sidebar .user-panel { padding: 16px 12px; border-bottom: 1px solid rgba(255,255,255,.06); }
		.sidebar .user-panel > .info { padding-left: 12px; }
		.sidebar .user-panel > .info > p { font-weight: 600; letter-spacing: -.01em; }
		.skin-red .sidebar-menu > li.header {
			color: #6f7b83; font-size: 11px; font-weight: 700;
			letter-spacing: .13em; text-transform: uppercase;
			padding: 18px 18px 9px; background: transparent;
		}
		.skin-red .sidebar-menu > li > a {
			padding: 11px 16px; border-left: 3px solid transparent;
			font-size: 14px; transition: background .15s ease, border-color .15s ease, color .15s ease;
		}
		.skin-red .sidebar-menu > li:hover > a,
		.skin-red .sidebar-menu > li > a:focus,
		.skin-red .sidebar-menu > li.active > a {
			background: #2b333a; color: #fff; border-left-color: #c0392b;
		}
		.skin-red .sidebar-menu > li > a > i { color: #8b969e; transition: color .15s ease; }
		.skin-red .sidebar-menu > li:hover > a > i,
		.skin-red .sidebar-menu > li.active > a > i { color: #e74c3c; }
		.skin-red .sidebar-menu > li > a > .fa-angle-left { color: inherit; }
		.skin-red .treeview-menu { background: #1a1f24; }
		.skin-red .treeview-menu > li > a { color: #aab4bb; padding: 9px 5px 9px 16px; }
		.skin-red .treeview-menu > li.active > a,
		.skin-red .treeview-menu > li > a:hover { color: #fff; }
		.skin-red .treeview-menu > li.active > a > i { color: #e74c3c; }

		/* Footer */
		.main-footer {
			border-top: 1px solid #ececec; background: #fff;
			color: #8a8f94; font-size: 13px; padding: 14px 20px;
		}
		.main-footer a { color: #c0392b; }
		.main-footer strong a:hover { text-decoration: underline; }
	</style>

	@stack('link')
</head>

<body class="hold-transition skin-red sidebar-mini">
	<div class="wrapper">
		@include('backend.partials.header')
		@include('backend.partials.sidebar')

		<div class="content-wrapper">
			<x-backend.breadcrumb :title="$title" :listNav="$listNav" />

			<section class="content">
				{{ $slot }}
			</section>
		</div>

		@include('backend.partials.footer')
	</div>

	<script src="{{ asset('assets') }}/backend/63e477e8/js/bootstrap.js"></script>
	<script src="{{ asset('assets') }}/backend/687d7f9a/js/app.min.js"></script>

	{{-- Script form input --}}
	@if (Str::contains(Request()->path(), ['/create', '/edit']))
		<script src="{{ asset('assets') }}/backend/8fbf74d4/js/datecontrol.js"></script>
		<script src="{{ asset('assets') }}/backend/85185e69/js/bootstrap-datepicker.js"></script>
		<script src="{{ asset('assets') }}/backend/85185e69/js/datepicker-kv.js"></script>
		<script src="{{ asset('assets') }}/backend/6a009609/js/kv-summernote.js"></script>
		<script src="//cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote.js"></script>
		<script src="{{ asset('assets') }}/backend/7fee4fda/js/fileinput.js"></script>
	@endif


	<script>
		const activeTab = localStorage.getItem('tabActive');

		$('.tab-item').removeClass('active'); // Hilangkan semua active dulu

		if (activeTab)
			$(`.tab-item[data-tab="${activeTab}"]`).addClass('active'); // Tambahkan active ke yang cocok
	</script>


	@stack('script')
</body>

</html>
