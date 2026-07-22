<x-layouts.backend title="Dashboard" :listNav="[['label' => 'User', 'route' => route('backend.user.index')], ['label' => ucfirst($data->username)]]">
	@php
		$imgProfil = checkFilePath(config('app.img_directory'), $data->picture)
		    ? asset('storage/' . config('app.img_directory') . $data->picture)
		    : asset('assets/img/default-user.jpg');
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-show / .ber-show) ── */
    .usr-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }

    /* Kartu profil */
    .usr-show .usr-card { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:10px; background:#fff; box-shadow:0 1px 2px rgba(31,35,40,.06); overflow:hidden; }
    .usr-show .usr-card .cover { height:74px; background:linear-gradient(135deg,#c0392b,#8e241a); }
    .usr-show .usr-card .body { padding:0 20px 20px; text-align:center; margin-top:-46px; }
    .usr-show .usr-avatar { width:92px; height:92px; border-radius:50%; object-fit:cover; aspect-ratio:1/1; border:4px solid #fff; box-shadow:0 3px 10px rgba(31,35,40,.15); background:#fff; }
    .usr-show .usr-name { font-size:18px; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:12px 0 2px; }
    .usr-show .usr-email { font-size:13px; color:var(--muted); margin:0 0 12px; word-break:break-word; }
    .usr-show .usr-status { display:inline-block; font-size:11px; font-weight:600; padding:3px 11px; border-radius:999px; }
    .usr-show .usr-status.on { background:#e5f3e9; color:#227a3b; }
    .usr-show .usr-status.off { background:#fdecea; color:#c0392b; }

    /* Definition list */
    .usr-show .usr-meta { margin:18px 0 0; border-top:1px solid var(--line); }
    .usr-show .usr-meta .row-item { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 2px; border-bottom:1px solid var(--line); font-size:13px; }
    .usr-show .usr-meta .row-item .k { color:var(--muted); font-weight:600; display:flex; align-items:center; gap:8px; }
    .usr-show .usr-meta .row-item .k i { color:var(--soft); width:15px; text-align:center; }
    .usr-show .usr-meta .row-item .v { color:var(--ink); font-variant-numeric:tabular-nums; }
    .usr-show .usr-meta .row-item .v .badge-role { background:#fdf2e0; color:#9a6a13; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; }
    .usr-show .usr-back { display:block; margin-top:16px; text-align:center; font-size:13px; color:var(--muted); }
    .usr-show .usr-back:hover { color:var(--accent); }

    /* Panel tab */
    .usr-show .nav-tabs-custom { border:1px solid var(--line); border-radius:10px; box-shadow:0 1px 2px rgba(31,35,40,.06); overflow:hidden; }
    .usr-show .nav-tabs-custom > .nav-tabs { border-bottom:1px solid var(--line); background:#faf9f7; padding:0 6px; margin:0; }
    .usr-show .nav-tabs-custom > .nav-tabs > li { margin-bottom:-1px; border-top:none; }
    .usr-show .nav-tabs-custom > .nav-tabs > li > a { color:var(--muted); font-size:13px; font-weight:600; border:none !important; border-bottom:2px solid transparent !important; border-radius:0; padding:14px 16px; background:transparent; }
    .usr-show .nav-tabs-custom > .nav-tabs > li > a:hover { color:var(--ink); background:transparent; }
    .usr-show .nav-tabs-custom > .nav-tabs > li.active > a { color:var(--accent); border-bottom-color:var(--accent) !important; background:transparent; }
    .usr-show .nav-tabs-custom > .nav-tabs > li.active { border-top:none; }
    .usr-show .nav-tabs-custom > .tab-content { padding:22px; }

    /* Section title dalam tab */
    .usr-show .tab-title { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:0 0 16px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .usr-show .tab-title::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    /* Timeline aktivitas */
    .usr-show .timeline-list { list-style:none; margin:0; padding:0; }
    .usr-show .timeline-list li { position:relative; padding:0 0 16px 26px; }
    .usr-show .timeline-list li::before { content:''; position:absolute; left:6px; top:16px; bottom:-2px; width:2px; background:var(--line); }
    .usr-show .timeline-list li:last-child::before { display:none; }
    .usr-show .timeline-list li .dot { position:absolute; left:0; top:4px; width:14px; height:14px; border-radius:50%; background:#fff; border:3px solid var(--accent); }
    .usr-show .timeline-list li .txt { font-size:13.5px; color:var(--ink); line-height:1.55; }
    .usr-show .timeline-list li .time { font-size:11.5px; color:var(--soft); margin-top:2px; }
    .usr-show .timeline-empty { color:var(--soft); font-size:13px; padding:8px 0; }

    /* Form ganti password / photo */
    .usr-show .form-group.required > .control-label::after { content:' *'; color:var(--accent); }
    .usr-show .control-label { color:var(--muted); font-weight:600; font-size:13px; margin-bottom:6px; }
    .usr-show .tab-content .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; height:40px; max-width:420px; transition:border-color .14s ease, box-shadow .14s ease; }
    .usr-show .tab-content input[type=file].form-control { height:auto; padding:8px 10px; }
    .usr-show .tab-content .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .usr-show .btn-accent { background:var(--accent); border-color:var(--accent); color:#fff; border-radius:6px; padding:9px 20px; font-weight:600; }
    .usr-show .btn-accent:hover, .usr-show .btn-accent:focus { background:#a5271b; border-color:#a5271b; color:#fff; }

    .usr-show .pagination { margin:6px 0 0; }
</style>
@endpush

	<section class="content usr-show">
		<div class="row">
			<div class="col-md-4">
				<div class="usr-card">
					<div class="cover"></div>
					<div class="body">
						<img class="usr-avatar" src="{{ $imgProfil }}" alt="{{ $data->username }}">
						<h3 class="usr-name">{{ ucfirst($data->username) }}</h3>
						<p class="usr-email">{{ $data->email }}</p>
						@if($data->status == 10)
							<span class="usr-status on"><i class="fa fa-check-circle"></i> Aktif</span>
						@else
							<span class="usr-status off"><i class="fa fa-ban"></i> Nonaktif</span>
						@endif

						<div class="usr-meta">
							<div class="row-item">
								<span class="k"><i class="fa fa-shield"></i> Hak Akses</span>
								<span class="v"><span class="badge-role">superadmin</span></span>
							</div>
							<div class="row-item">
								<span class="k"><i class="fa fa-bolt"></i> Jumlah Aktivitas</span>
								<span class="v">{{ number_format($logCount) }}</span>
							</div>
							<div class="row-item">
								<span class="k"><i class="fa fa-calendar-o"></i> Tanggal dibuat</span>
								<span class="v">{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y') }}</span>
							</div>
						</div>

						<a href="{{ route('backend.user.index') }}" class="usr-back">
							<i class="fa fa-arrow-left"></i> Kembali ke daftar user
						</a>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs dashboard_tabs_cl">
						<li @class(['active' => !session('tabActive')])>
							<a href="#timeline" data-toggle="tab" aria-expanded="false"><i class="fa fa-history"></i> Aktivitas</a>
						</li>
						<li @class(['active' => session('tabActive') == 'tabUpdatePassword'])>
							<a href="#tabUpdatePassword" data-toggle="tab" aria-expanded="true"><i class="fa fa-key"></i> Ganti Password</a>
						</li>
						<li @class(['active' => session('tabActive') == 'tabUpdateImage'])>
							<a href="#password" data-toggle="tab" aria-expanded="true"><i class="fa fa-camera"></i> Ganti Photo Profil</a>
						</li>
					</ul>

					<div class="tab-content">
						{{-- Aktivitas --}}
						<div @class(['tab-pane', 'active' => !session('tabActive')]) id="timeline">
							<div class="tab-title">Log Aktivitas</div>

							@if(count($logUser) > 0)
								<ul class="timeline-list">
									@foreach ($logUser as $item)
										<li>
											<span class="dot"></span>
											<div class="txt">{{ $item->keterangan }}</div>
											@if(!empty($item->created_at))
												<div class="time"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y, H:i') }}</div>
											@endif
										</li>
									@endforeach
								</ul>
								{{ $logUser->links() }}
							@else
								<div class="timeline-empty">Belum ada aktivitas tercatat.</div>
							@endif
						</div>

						{{-- Ganti Password --}}
						<div @class(['tab-pane', 'active' => session('tabActive') == 'tabUpdatePassword']) id="tabUpdatePassword">
							<div class="tab-title">Ganti Password</div>
							<form id="form-change" action="{{ route('backend.change-password', $data->id) }}" method="post">
								@csrf

								<div class="form-group required">
									<label class="control-label" for="old_password">Password lama</label>
									<input type="password" id="old_password" class="form-control" name="old_password" required>
									@error('old_password')
										<p class="help-block help-block-error" style="color:#c0392b">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group required">
									<label class="control-label" for="new_password">Password Baru</label>
									<input type="password" id="new_password" class="form-control" name="new_password" required>
									@error('new_password')
										<p class="help-block help-block-error" style="color:#c0392b">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group required">
									<label class="control-label" for="password_confirmation">Ulangi Password Baru</label>
									<input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required>
									@error('password_confirmation')
										<p class="help-block help-block-error" style="color:#c0392b">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-accent"><i class="fa fa-save"></i> Ganti Password</button>
								</div>
							</form>
						</div>

						{{-- Ganti Photo Profil --}}
						<div @class(['tab-pane', 'active' => session('tabActive') == 'tabUpdateImage']) id="password">
							<div class="tab-title">Ganti Photo Profil</div>
							<form id="form-change-picture" action="{{ route('backend.change-image-profil', $data->id) }}" method="post"
								enctype="multipart/form-data">
								@csrf

								<div class="form-group required">
									<label class="control-label" for="imgProfil">Picture</label>
									<input name="imgProfil" type="file" id="imgProfil" class="form-control" accept="images/*" required>
									@error('imgProfil')
										<p class="help-block help-block-error" style="color:#c0392b">{{ $message }}</p>
									@enderror
									<p class="help-block" style="font-size:12px;color:#8b9096;margin-top:6px;">Format JPG/PNG, rasio 1:1 lebih disarankan.</p>
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-accent" name="change-button2"><i class="fa fa-upload"></i> Ganti Photo</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

</x-layouts.backend>
