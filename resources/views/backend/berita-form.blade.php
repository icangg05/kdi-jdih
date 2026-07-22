<x-layouts.backend :title="$title" :listNav="[['label' => 'Berita', 'route' => route('backend.berita.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.berita.create') ? true : false;
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .peng-form / .vid-form) ── */
    .ber-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    .ber-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .ber-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .ber-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .ber-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .ber-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Aksen per peran box */
    .ber-form .box.box-info    { border-top-color:#2f6fb0; }
    .ber-form .box.box-info    > .box-header::before { background:#2f6fb0; }
    .ber-form .box.box-success { border-top-color:#2b9348; }
    .ber-form .box.box-success > .box-header::before { background:#2b9348; }

    /* Label & input */
    .ber-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .ber-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; transition:border-color .14s ease, box-shadow .14s ease; }
    .ber-form input.form-control:not(.file-loading) { height:38px; }
    .ber-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .ber-form .form-group { margin-bottom:16px; }
    .ber-form .help-block { font-size:12px; color:var(--soft); }
    .ber-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }

    /* Select2 & summernote selaras */
    .ber-form .select2-container--default .select2-selection--single { border-color:#dcd9d3 !important; border-radius:6px; }
    .ber-form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .ber-form input[type="checkbox"] { margin-right:5px; }

    /* Baris aksi */
    .ber-form .box-footer { background:transparent; border-top:none; padding:4px 0 0; }
    .ber-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .ber-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .ber-form .btn-success.btn-flat:hover, .ber-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .ber-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .ber-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
</style>
@endpush

	<div class="box-body no-padding">
		<div class="section ber-form">
			<form class="form-horizontal"
				action="{{ $isCreate ? route('backend.berita.store') : route('backend.berita.update', $data->id) }}"
				method="POST" enctype="multipart/form-data">
				@csrf
				@if (!$isCreate)
					@method('PATCH')
				@endif

				{{-- INFORMASI BERITA --}}
				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>Informasi Berita</b>
					</div>
					<div class="box-body">
						<x-backend.input.date
							label="Tanggal"
							key="tanggal"
							:value="$data->tanggal ?? ''"
							required
							placeholder="Tulis tanggal berita" />

						<x-backend.input.text
							label="Judul"
							key="judul"
							:value="$data->judul ?? ''"
							required />

						<x-backend.input.editor-quill
							label="Isi"
							key="isi"
							:value="$data->isi ?? ''"
							required
							placeholder="Tulis isi berita..." />
					</div>
				</div>

				{{-- MEDIA --}}
				<div class="box box-info box-solid">
					<div class="box-header with-border">
						<b>Media</b>
					</div>
					<div class="box-body">
						<x-backend.input.file
							label="Sampul Berita"
							key="image"
							:value="$data->image ?? ''"
							:mimes="['jpg', 'jpeg', 'png']" />
					</div>
				</div>

				{{-- PUBLIKASI --}}
				<div class="box box-success box-solid">
					<div class="box-header with-border">
						<b>Publikasi</b>
					</div>
					<div class="box-body">
						<x-backend.input.select
							label="Status"
							key="status"
							:value="$data->status ?? ''"
							placeholder="Pilih Status..."
							required
							:data="[['label' => 'Publish', 'value' => 1], ['label' => 'Tidak Publish', 'value' => 0]]" />

						@include('backend.partials.auto-translate-checkbox')
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-success btn-flat">
							<i class="fa fa-save"></i> Simpan</button>
						<a class="btn btn-danger btn-flat" href="{{ route('backend.berita.index') }}">
							<i class="fa fa-remove"></i> Batal</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</x-layouts.backend>
