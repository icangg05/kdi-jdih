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
	@if (Str::contains(Request()->path(), ['/create', '/edit', '/narasi']))
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
