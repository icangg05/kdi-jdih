<section class="dokumen">
	<div class="container">

		@include('frontend.partials.search-dokumen')

		<div class="row">
			<div class="col-lg-9 sm-margin-50px-bottom">
				<div id="w0" class="list-view">
					@forelse ($data as $item)
						<div class="item" data-key="509">
							<div class="border-bottom margin-40px-bottom padding-40px-bottom xs-padding-20px-bottom">
								<div class="card card-list border-0">
									<div class="row align-items-center">
										<div class="card-body no-padding-tb">
											<div class="d-flex justify-content-between align-items-center">
												<p class="mb-1">
													<span>
														@if ($kategori === 'monografi' || $kategori === 'artikel')
															TAHUN TERBIT ({{ $item->tahun_terbit }})
														@else
															{{ __($item->bentuk_peraturan) }} ({{ $item->tahun_terbit }})
														@endif
													</span>
												</p>
											</div>
											<p style="line-height: 24px;">
												<a wire:navigate class="titles" href="{{ route('frontend.dokumen.show', [$kategori, $item->id]) }}">
													{{ $item->judul }}</a>
											</p>
											<div class="d-flex left-content-between align-items-left">
												<form action="{{ route('download_file') }}" method="POST" style="display: inline">
													@csrf
													<input type="hidden" name="filePath"
														value="{{ config('app.doc_directory') . $item->dokumen_lampiran }}">
													<button @disabled(!checkFilePath(config('app.doc_directory'), $item->dokumen_lampiran))
														onclick="window.location='{{ $item->dokumen_lampiran ? route('download_file', $item->dokumen_lampiran) : '#' }}'"
														style="opacity: {{ !checkFilePath(config('app.doc_directory'), $item->dokumen_lampiran) ? '.4' : '1' }};"
														class="btn-custom mr-3">
														<i class="fa-solid fa-file-lines"></i>&nbsp; Download
													</button>
												</form>

												<form action="{{ route('download_file') }}" method="POST" style="display: inline">
													@csrf
													<input type="hidden" name="filePath"
														value="{{ config('app.doc_directory') . $item->abstrak }}">
													<button @disabled(!checkFilePath(config('app.doc_directory'), $item->abstrak))
														onclick="window.location='{{ $item->abstrak ? route('download_file', $item->abstrak) : '#' }}'"
														style="opacity: {{ !checkFilePath(config('app.doc_directory'), $item->abstrak) ? '.4' : '1' }};"
														class="btn-custom-2 mr-3">
														<i class="fa-solid fa-file-lines"></i>&nbsp; Abstrak
													</button>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					@empty
						<div class="empty">No results found.</div>
					@endforelse

					{{ $data->onEachSide(3)->links() }}
				</div>
			</div>

			<div class="col-lg-3">
				<div class="side-bar">
					<div class="widget">
						<div class="widget-title">
							<h3>Pencarian</h3>
						</div>
						<div class="dokumen-search">
							<form action="{{ route('frontend.dokumen.index', $kategori) }}" method="GET">
								@php $exceptKey = ['jenis', 'nomor', 'tahun', 'status'] @endphp
								@foreach (request()->except($exceptKey) as $key => $value)
									<input type="hidden" name="{{ $key }}" value="{{ $value }}">
								@endforeach

								<div class="form-group field-dokumensearch-jenis_peraturan">
									<label class="control-label" for="jenis">Jenis Dokumen</label>
									<select id="jenis" class="form-control" name="jenis">
										<option value="">Pilih Jenis Dokumen</option>
										@foreach ($tipeDokumen as $item)
											<option @selected(request()->jenis === $item->name) value="{{ $item->name }}">{{ $item->name }}</option>
										@endforeach
									</select>
									<div class="help-block"></div>
								</div>
								<div class="form-group field-dokumensearch-nomor_peraturan">
									<label class="control-label" for="nomor">Nomor Peraturan</label>
									<input value="{{ request()->nomor }}" type="text" id="nomor" class="form-control"
										name="nomor">
									<div class="help-block"></div>
								</div>
								<div class="form-group field-dokumensearch-tahun_terbit">
									<label class="control-label" for="tahun">Tahun Terbit</label>
									<input type="text" id="tahun" class="form-control" name="tahun"
										value="{{ request()->tahun }}">
									<div class="help-block"></div>
								</div>
								<div class="form-group field-dokumensearch-status_terakhir">
									<label class="control-label" for="status">Status</label>
									<select id="status" class="form-control" name="status">
										<option value="">Pilih Status</option>
										<option @selected(request()->status === 'dicabut') value="dicabut">dicabut</option>
										<option @selected(request()->status == 'mencabut') value="mencabut">mencabut</option>
										<option @selected(request()->status == 'diubah') value="diubah">diubah</option>
										<option @selected(request()->status == 'mengubah') value="mengubah">mengubah</option>
										<option @selected(request()->status == 'Tidak memiliki daya guna') value="Tidak memiliki daya guna">Tidak memiliki daya guna
										</option>
									</select>
									<div class="help-block"></div>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-primary">Filter</button>
									<button type="button" class="btn btn-secondary"
										onclick="window.location='{{ route('frontend.dokumen.index', $kategori) }}'">Clear</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
