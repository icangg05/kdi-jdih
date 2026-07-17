<x-layouts.backend title="Dashboard" :listNav="[['label' => 'User', 'route' => route('backend.user.index')], ['label' => ucfirst($data->username)]]">
	@php
		$imgProfil = checkFilePath(config('app.img_directory'), $data->picture)
		    ? asset('storage/' . config('app.img_directory') . $data->picture)
		    : asset('assets/img/default-user.jpg');
	@endphp

	<section class="content">
		<div class="row">
			<div class="col-md-3">
				<div class="box box-primary">
					<div class="box-body box-profile">

						<img class="profile-user-img img-responsive img-circle" src="{{ $imgProfil }}"
							width="160" height="160" alt="myImage" style="object-fit: cover; aspect-ratio: 1/1;">
						<h3 class="profile-username text-center">{{ $data->username }}</h3>
						<p class="text-muted text-center">{{ $data->email }}</p>
						<ul class="list-group list-group-unbordered">
							<li class="list-group-item">
								<b>Hak Akses</b>
								<a class="pull-right">
									<span class="label label-warning label-md">superadmin</span>&nbsp; </a>
							</li>
							<li class="list-group-item">
								<b>Jumlah Aktivitas</b> <a class="pull-right">{{ $logCount }}</a>
							</li>
							<li class="list-group-item">
								<b>Tanggal dibuat</b> <a class="pull-right">
									{{ Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y') }}
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="col-md-9">
				<div class="nav-tabs-custom">

					<ul class="nav nav-tabs dashboard_tabs_cl">
						<li @class(['active' => !session('tabActive')])><a href="#timeline" data-toggle="tab" aria-expanded="false">Aktivitas</a></li>
						<li @class(['active' => session('tabActive') == 'tabUpdatePassword'])>
							<a href="#tabUpdatePassword" data-toggle="tab" aria-expanded="true">Ganti
								Password</a>
						</li>
						<li @class(['active' => session('tabActive') == 'tabUpdateImage'])>
							<a href="#password" data-toggle="tab" aria-expanded="true">Ganti Photo Profil</a>
						</li>
					</ul>
					<div class="tab-content">
						<!-- /.tab-pane -->
						<div @class(['tab-pane', 'active' => !session('tabActive')]) id="timeline">
							<div id="w0" class="grid-view is-bs3 hide-resize" data-krajee-grid="kvGridInit_1056841c"
								data-krajee-ps="ps_w0_container">
								<div id="w0-container" class="table-responsive kv-grid-container">
									<table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
										<thead>
											<tr>
												<th class="text-center">No</th>
												<th>Keterangan</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($logUser as $item)
												<tr>
													<td class="text-center" style="width: 50px;">
														{{ $loop->index + $logUser->firstItem() }}.
													</td>
													<td>
														{{ $item->keterangan }}
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>

								{{ $logUser->links() }}
							</div>
						</div>
						<!-- /.tab-pane -->

						<div @class([
							'tab-pane',
							'active' => session('tabActive') == 'tabUpdatePassword',
						]) id="tabUpdatePassword">
							<form id="form-change" action="{{ route('backend.change-password', $data->id) }}" method="post">
								@csrf

								<div class="form-group required">
									<label class="control-label" for="old_password">Password lama</label>
									<input type="password" id="old_password" class="form-control"
										name="old_password" required>
									@error('old_password')
										<p class="help-block help-block-error" style="color: red">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group required">
									<label class="control-label" for="new_password">Password Baru</label>
									<input type="password" id="new_password" class="form-control"
										name="new_password" required>
									@error('new_password')
										<p class="help-block help-block-error" style="color: red">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group required">
									<label class="control-label" for="password_confirmation">Ulangi Password Baru</label>
									<input type="password" id="password_confirmation" class="form-control"
										name="password_confirmation" required>
									@error('password_confirmation')
										<p class="help-block help-block-error" style="color: red">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary">Ganti Password</button>
								</div>
							</form>
						</div>

						<div @class([
							'tab-pane',
							'active' => session('tabActive') == 'tabUpdateImage',
						]) id="password">
							<form id="form-change-picture" action="{{ route('backend.change-image-profil', $data->id) }}" method="post"
								enctype="multipart/form-data">
								@csrf

								<div class="form-group required">
									<label class="control-label" for="imgProfil">Picture</label>
									<input name="imgProfil" type="file" id="imgProfil" class="form-control" accept="images/*" required>

									@error('imgProfil')
										<p class="help-block help-block-error" style="color: red">{{ $message }}</p>
									@enderror
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary" name="change-button2">Ganti Photo</button>
								</div>
							</form>
						</div>
						<!-- /.tab-pane -->
					</div>
					<!-- /.tab-content -->
				</div>
				<!-- /.nav-tabs-custom -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

	</section>

</x-layouts.backend>
