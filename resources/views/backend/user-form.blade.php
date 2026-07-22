<x-layouts.backend :title="$title" :listNav="[['label' => 'User', 'route' => route('backend.user.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.user.create') ? true : false;
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .peng-form / .usr-show) ── */
    .usr-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:760px; }

    .usr-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .usr-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .usr-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .usr-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .usr-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    .usr-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .usr-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; height:38px; transition:border-color .14s ease, box-shadow .14s ease; }
    .usr-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .usr-form .form-group { margin-bottom:16px; }
    .usr-form .help-block { font-size:12px; color:var(--soft); }

    .usr-form .box-footer { background:transparent; border-top:none; padding:4px 0 0; }
    .usr-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .usr-form .btn-primary.btn-flat { background:var(--accent); border-color:var(--accent); }
    .usr-form .btn-primary.btn-flat:hover, .usr-form .btn-primary.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .usr-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .usr-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
</style>
@endpush

	<div class="box-body no-padding">
		<div class="section usr-form">
			<form class="form-horizontal"
				action="{{ $isCreate ? route('backend.user.store') : route('backend.user.update', $data->id) }}"
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

						<x-backend.input.text
							label="Username"
							key="username"
							:value="$data->username ?? ''"
							required />

						<x-backend.input.text
							label="Email"
							key="email"
							:value="$data->email ?? ''"
							required />

						<x-backend.input.text
							label="Password"
							key="password"
							type="password"
							required />

						<x-backend.input.text
							label="Konfirmasi Password"
							key="password_confirmation"
							type="password"
							required />

					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-flat">
							<i class="fa fa-save"></i> Sign Up</button>
						<a class="btn btn-danger btn-flat" href="{{ route('backend.user.index') }}">
							<i class="fa fa-remove"></i> Batal</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</x-layouts.backend>
