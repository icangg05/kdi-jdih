<x-layouts.backend :title="$title" :listNav="[['label' => 'Video', 'route' => route('backend.video.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.video.create') ? true : false;
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .peng-form / .puu-form) ── */
    .vid-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:820px; }

    .vid-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .vid-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .vid-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .vid-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .vid-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Catatan link */
    .vid-form .note { background:#faf9f7; border:1px solid var(--line); border-radius:7px; padding:11px 14px; font-size:12.5px; color:var(--muted); line-height:1.6; margin-bottom:18px; display:flex; gap:9px; }
    .vid-form .note i { color:var(--accent); margin-top:2px; }
    .vid-form .note code { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; background:#f2efe9; color:#5a4b3f; padding:1px 6px; border-radius:4px; }

    /* Label & input */
    .vid-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .vid-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; height:38px; transition:border-color .14s ease, box-shadow .14s ease; }
    .vid-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .vid-form .form-group { margin-bottom:16px; }
    .vid-form .help-block { font-size:12px; color:var(--soft); }
    .vid-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }

    /* Baris aksi */
    .vid-form .box-footer { background:transparent; border-top:none; padding:4px 0 0; }
    .vid-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .vid-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .vid-form .btn-success.btn-flat:hover, .vid-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .vid-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .vid-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
</style>
@endpush

	<div class="box-body no-padding">
		<div class="section vid-form">
			<form class="form-horizontal"
				action="{{ $isCreate ? route('backend.video.store') : route('backend.video.update', $data->id) }}"
				method="POST" enctype="multipart/form-data">
				@csrf
				@if (!$isCreate)
					@method('PATCH')
				@endif

				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>Form {{ $title }}</b>
					</div>
					<div class="box-body">

						<div class="note">
							<i class="fa fa-info-circle"></i>
							<span>Kolom <b>Link</b> diisi <b>kode video YouTube</b> saja — bagian setelah <code>v=</code> pada URL.
							Contoh: dari <code>youtube.com/watch?v=dQw4w9WgXcQ</code> isikan <code>dQw4w9WgXcQ</code>.</span>
						</div>

						<x-backend.input.date
							label="Tanggal"
							key="tanggal"
							:value="$data->tanggal ?? ''"
							required
							placeholder="Tulis tanggal video" />

						<x-backend.input.text
							label="Judul"
							key="judul"
							:value="$data->judul ?? ''"
							required />

						<x-backend.input.text
							label="Link"
							key="link"
							:value="$data->link ?? ''"
							placeholder="cth. dQw4w9WgXcQ"
							required />

					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-success btn-flat">
							<i class="fa fa-save"></i> Simpan</button>
						<a class="btn btn-danger btn-flat" href="{{ route('backend.video.index') }}">
							<i class="fa fa-remove"></i> Batal</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</x-layouts.backend>
