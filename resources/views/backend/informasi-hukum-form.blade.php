<x-layouts.backend :title="$title" :listNav="[['label' => 'Informasi Hukum', 'route' => route('backend.informasi-hukum.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.informasi-hukum.create') ? true : false;
		$selectJenisInfokum = DB::table('jenis_informasi_hukum')
			->get()
			->map(fn($item) => ['label' => "$item->name — $item->singkatan", 'value' => $item->id]);
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .peng-form / .ber-form) ── */
    .ih-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    .ih-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .ih-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .ih-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .ih-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .ih-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    .ih-form .box.box-info    { border-top-color:#2f6fb0; }
    .ih-form .box.box-info    > .box-header::before { background:#2f6fb0; }
    .ih-form .box.box-success { border-top-color:#2b9348; }
    .ih-form .box.box-success > .box-header::before { background:#2b9348; }

    .ih-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .ih-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; transition:border-color .14s ease, box-shadow .14s ease; }
    .ih-form input.form-control:not(.file-loading) { height:38px; }
    .ih-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .ih-form .form-group { margin-bottom:16px; }
    .ih-form .help-block { font-size:12px; color:var(--soft); }
    .ih-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }

    .ih-form .select2-container--default .select2-selection--single { border-color:#dcd9d3 !important; border-radius:6px; }
    .ih-form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .ih-form input[type="checkbox"] { margin-right:5px; }

    .ih-form .box-footer { background:transparent; border-top:none; padding:4px 0 0; }
    .ih-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .ih-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .ih-form .btn-success.btn-flat:hover, .ih-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .ih-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .ih-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
</style>
@endpush

	<div class="box-body no-padding">
		<div class="section ih-form">
			<form class="form-horizontal"
				action="{{ $isCreate ? route('backend.informasi-hukum.store') : route('backend.informasi-hukum.update', $data->id) }}"
				method="POST" enctype="multipart/form-data">
				@csrf
				@if (!$isCreate)
					@method('PATCH')
				@endif

				{{-- INFORMASI --}}
				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>Informasi Hukum</b>
					</div>
					<div class="box-body">
						<x-backend.input.select
							label="Jenis Informasi Hukum"
							key="jenis"
							:value="$data->jenis ?? ''"
							placeholder="--Pilih jenis informasi hukum--"
							required
							:data="$selectJenisInfokum" />

						<x-backend.input.date
							label="Tanggal"
							key="tanggal"
							:value="$data->tanggal ?? ''"
							required
							placeholder="Tulis tanggal informasi hukum" />

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
							placeholder="Tulis isi informasi hukum..." />
					</div>
				</div>

				{{-- MEDIA & DOKUMEN --}}
				<div class="box box-info box-solid">
					<div class="box-header with-border">
						<b>Media &amp; Dokumen</b>
					</div>
					<div class="box-body">
						<x-backend.input.file
							label="Sampul"
							key="image"
							:value="$data->image ?? ''"
							:mimes="['jpg', 'jpeg', 'png']" />

						<x-backend.input.file
							label="Dokumen"
							key="dokumen"
							:value="$data->dokumen ?? ''"
							:mimes="['pdf']" />
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
						<a class="btn btn-danger btn-flat" href="{{ route('backend.informasi-hukum.index') }}">
							<i class="fa fa-remove"></i> Batal</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</x-layouts.backend>
