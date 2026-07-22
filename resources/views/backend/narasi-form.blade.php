<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .peng-form / .ber-form) ── */
    .nar-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    .nar-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .nar-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .nar-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .nar-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .nar-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Catatan */
    .nar-form .note { background:#faf9f7; border:1px solid var(--line); border-radius:7px; padding:11px 14px; font-size:12.5px; color:var(--muted); line-height:1.6; margin-bottom:18px; display:flex; gap:9px; }
    .nar-form .note i { color:var(--accent); margin-top:2px; }

    .nar-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .nar-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; transition:border-color .14s ease, box-shadow .14s ease; }
    .nar-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .nar-form .form-group { margin-bottom:16px; }
    .nar-form input[type="checkbox"] { margin-right:5px; }
    .nar-form .help-block { font-size:12px; color:var(--soft); }

    .nar-form .box-footer { background:transparent; border-top:1px solid var(--line); padding:15px 22px; }
    .nar-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .nar-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .nar-form .btn-success.btn-flat:hover, .nar-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
</style>
@endpush

	<div class="box-body no-padding">
		<div class="section nar-form">
			<form class="form-horizontal"
				action="{{ route('backend.narasi.update', $data->id) }}"
				method="POST" enctype="multipart/form-data">
				@csrf
				@method('PATCH')

				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>{{ $title }}</b>
					</div>
					<div class="box-body">

						<div class="note">
							<i class="fa fa-info-circle"></i>
							<span>Narasi &amp; kutipan ini tampil di halaman depan situs. Perubahan langsung berlaku setelah disimpan.</span>
						</div>

						<x-backend.input.editor-quill
							label="Narasi & Qoute"
							key="text"
							:value="$data->text ?? ''"
							required
							placeholder="Tulis isi narasi..." />

						@include('backend.partials.auto-translate-checkbox')
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-success btn-flat">
							<i class="fa fa-save"></i> Simpan
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</x-layouts.backend>
