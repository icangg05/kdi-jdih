<div id="p0">
	<div class="box-body table-responsive no-padding">
		<div id="w0" class="grid-view is-bs3 hide-resize">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="pull-right">
						Ditampilkan {{ $data->firstItem() }} - {{ $data->lastItem() }} dari {{ $data->total() }}
						Data</div>
					<h3 class="panel-title">
						<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> &nbsp;Data {{ $title }}</h3>
					</h3>
					<div class="clearfix"></div>
				</div>
				<div class="kv-panel-before" style="padding-top: 10px; padding-right: 10px">
					<div class="btn-toolbar kv-grid-toolbar toolbar-container pull-right">
						<div class="btn-group">
							<a onclick="localStorage.setItem('tabActive', 'dataUtama');" class="btn btn-success"
								href="{{ route("backend.$prefixRoute.create") }}">
								<i class="fa fa-plus-circle"></i> Tambah Data
							</a>
						</div>
						<div class="btn-group">
							<button id="w1" class="btn btn-default dropdown-toggle" title="Export" data-toggle="dropdown"><i
									class='glyphicon glyphicon-export'></i> <span class="caret"></span></button>
							<ul id="w2" class="dropdown-menu dropdown-menu-right">
								<li role="presentation" class="dropdown-header">Export Page Data</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-info glyphicon glyphicon-save"></i> HTML
									</a>
								</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
									</a>
								</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-muted glyphicon glyphicon-floppy-save"></i> Text
									</a>
								</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel
									</a>
								</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF
									</a>
								</li>
								<li>
									<a class="export-html" href="#">
										<i class="text-warning glyphicon glyphicon-floppy-open"></i> JSON
									</a>
								</li>
							</ul>
						</div>
						<div class="btn-group">
							<a id="w0-togdata-page" class="btn btn-default"
								href="#" title="Show all data">
								<i class='glyphicon glyphicon-resize-full'></i> All
							</a>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div id="w0-container" class="table-responsive kv-grid-container">
					<table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
						<thead>
							<tr>
								<th class="text-center">No</th>
								@foreach ($columns as $item)
									<th class="text-nowrap">
										<a href="#">{{ $item['title'] }}</a>
									</th>
								@endforeach
								<th class="text-center" style="width: 150px;">Aksi</th>
							</tr>
							<tr id="w0-filters" class="filters skip-export">
								<td>&nbsp;</td>

								<form action="{{ route("backend.$prefixRoute.index") }}" method="get" id="filter-form">
									@foreach ($columns as $col)
										<td>
											@if (!empty($col['type_search']) && $col['type_search'] === 'select')
												<select id="{{ $col['key'] . '-select' }}"
													name="{{ $col['key'] }}"
													class="form-control {{ $col['key'] }}"
													data-autosubmit="true">
													<option value="">Pilih...</option>
													@foreach ($col['data_type_search'] as $item)
														<option value="{{ is_array($item) ? $item['value'] : $item }}"
															{{ request($col['key']) == (is_array($item) ? $item['value'] : $item) ? 'selected' : '' }}>
															{!! is_array($item) ? $item['label'] : ucfirst($item) !!}
														</option>
													@endforeach
												</select>
											@else
												<input value="{{ request($col['key']) }}"
													type="text"
													class="form-control"
													name="{{ $col['key'] }}"
													autocomplete="off">
											@endif
										</td>
									@endforeach
								</form>

								@push('script')
									<script>
										$(document).ready(function() {
											$('select[data-autosubmit="true"]').each(function() {
												$(this).select2();
											});

											$(document).on('change', 'select[data-autosubmit="true"]', function() {
												$('#filter-form').submit(); // Form tunggal
											});

											$(document).on('keypress', 'input.form-control', function(e) {
												if (e.which == 13) {
													$('#filter-form').submit();
												}
											});
										});
									</script>
								@endpush

								<td>&nbsp;</td>
							</tr>
						</thead>
						<tbody>
							@forelse ($data as $item)
								@php $item = (array) $item; @endphp
								<tr class="w0">
									<td class="text-center" style="width: 50px;">
										{{ $loop->index + $data->firstItem() }}.
									</td>

									@foreach ($columns as $col)
										@php
											$value = !empty($item[$col['key']]) ? $item[$col['key']] : '—';
											// $value = $item[$col['key']] ?? '—';
											if (!empty($col['strip'])) {
											    $value = strip_tags($value);
											}
											if (!empty($col['limit'])) {
											    $value = Str::limit($value, $col['limit']);
											}
											if (!empty($col['format']) && $col['format'] === 'date') {
											    $value = \Carbon\Carbon::parse($value)->translatedFormat('d F Y');
											}
											if (!empty($col['format']) && $col['format'] === 'ucfirst') {
											    $value = ucfirst($value);
											}
										@endphp
										<td>
											@if (!empty($col['format']) && $col['format'] === 'href')
												<a onclick="localStorage.setItem('tabActive', 'dataUtama');"
													href="{{ route("backend.$prefixRoute.show", $item['id']) }}">{!! $value !!}</a>
											@else
												{!! $value !!}
											@endif
										</td>
									@endforeach


									@if (request()->routeIs('backend.user.index'))
										<td class="text-start">
											<a href="{{ route("backend.$prefixRoute.show", $item['id']) }}">
												<span class="btn btn-sm btn-primary">
													<b class="fa fa-eye"></b></span>&nbsp;
											</a>

											@if (auth()->user()->id != $item['id'])
												@if ($item['status'] == 10)
													<a href="{{ route('backend.change-active-user', [$item['id'], $item['status']]) }}">
														<span class="btn btn-sm btn-success">
															<b class="fa fa-check-square"></b></span>&nbsp;
													</a>
												@else
													<a href="{{ route('backend.change-active-user', [$item['id'], $item['status']]) }}">
														<span class="btn btn-sm btn-warning">
															<b class="fa fa-ban"></b></span>&nbsp;
													</a>
												@endif
											@endif

											@if (auth()->user()->id != $item['id'])
												<form style="display: inline" action="{{ route("backend.$prefixRoute.destroy", $item['id']) }}"
													method="post">
													@csrf
													@method('delete')

													<button style="outline: none;"
														type="submit"
														class="btn btn-sm btn-danger" onclick="return confirm('Yakin akan menghapus data ini?')">
														<b class="fa fa-trash"></b>
													</button>
												</form>
											@endif
										</td>
									@else
										<td class="text-center">
											<a onclick="localStorage.setItem('tabActive', 'dataUtama');"
												href="{{ route("backend.$prefixRoute.show", $item['id']) }}">
												<span class="btn btn-sm btn-success">
													<b class="fa fa-search-plus"></b></span>&nbsp;
											</a>
											<a onclick="localStorage.setItem('tabActive', 'dataUtama');"
												href="{{ route("backend.$prefixRoute.edit", $item['id']) }}">
												<span class="btn btn-sm btn-warning">
													<b class="fa fa-pencil"></b></span>&nbsp;
											</a>

											<form style="display: inline" action="{{ route("backend.$prefixRoute.destroy", $item['id']) }}"
												method="post">
												@csrf
												@method('delete')

												<button style="outline: none;"
													type="submit"
													class="btn btn-sm btn-danger" onclick="return confirm('Yakin akan menghapus data ini?')">
													<b class="fa fa-trash"></b>
												</button>
											</form>
										</td>
									@endif

								</tr>
							@empty
								<tr>
									<td class="text-center" colspan="{{ count($columns) + 2 }}">No results found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				<div class="kv-panel-after"></div>
				<div class="panel-footer">
					<div class="kv-panel-pager">
						{{ $data->onEachSide(3)->links() }}
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
