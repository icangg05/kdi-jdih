<x-layouts.backend :title="$title" :listNav="$listNav">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs dashboard_tabs_cl">
			<li class="tab-item" data-tab="dataUtama" @class(['active' => !session('tabActive')])
				onclick="localStorage.setItem('tabActive', 'dataUtama');">
				<a href="#tab_1" data-toggle="tab">Data Utama</a>
			</li>
			<li class="tab-item" data-tab="dataTeu" @class(['active' => session('tabActive') == 'dataTeu'])
				onclick="localStorage.setItem('tabActive', 'dataTeu');">
				<a href="#tab_2" data-toggle="tab">T.E.U</a>
			</li>
			<li class="tab-item" data-tab="dataSubjek" @class(['active' => session('tabActive') == 'dataSubjek'])
				onclick="localStorage.setItem('tabActive', 'dataSubjek');">
				<a href="#tab_3" data-toggle="tab">Subjek</a>
			</li>

			{{ $tabPane ?? '' }}
		</ul>

		<div class="tab-content">
			{{-- Tab data utama --}}
			{{ $slot }}

			@php
				$dataPengarang = DB::table('data_pengarang')
				    ->where('id_dokumen', $data->id)
				    ->leftJoin('pengarang', 'data_pengarang.nama_pengarang', '=', 'pengarang.id')
				    ->leftJoin('tipe_pengarang', 'data_pengarang.tipe_pengarang', '=', 'tipe_pengarang.id')
				    ->leftJoin('jenis_pengarang', 'data_pengarang.jenis_pengarang', '=', 'jenis_pengarang.id')
				    ->select(
				        'data_pengarang.id',
				        'pengarang.name as nama_pengarang',
				        'tipe_pengarang.name as tipe_pengarang',
				        'jenis_pengarang.name as jenis_pengarang',
				    )
				    ->get();
			@endphp
			{{-- Tab data TEU --}}
			<div class="tab-pane tab-item" data-tab="dataTeu" id="tab_2">
				<div class="box-header">
					<a class="btn btn-success btn-flat" href="{{ route('backend.form_teu.create', $data->id) }}">
						<i class="fa fa-plus-circle"></i> Tambah TEU
					</a>
					<p></p>
					<div id="w1" class="grid-view is-bs3 hide-resize">
						<div id="w1-container" class="table-responsive kv-grid-container">
							<table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
								<thead>
									<tr>
										<th class="text-center" style="width: 60px;">No</th>
										<th>Nama T.E.U</th>
										<th style="width: 170px;">Tipe T.E.U</th>
										<th style="width: 170px;">Jenis T.E.U</th>
										<th class="text-center" style="width: 150px;">Aksi</th>
									</tr>
								</thead>
								<tbody>
									@forelse ($dataPengarang as $item)
										<tr>
											<td class="text-center">{{ $loop->iteration }}.</td>
											<td>{{ $item->nama_pengarang }}</td>
											<td>{{ $item->tipe_pengarang }}</td>
											<td>{{ $item->jenis_pengarang }}</td>
											<td class="text-center">
												<form style="display: inline" action="{{ route('backend.form_teu.destroy', [$data->id, $item->id]) }}"
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
										</tr>
									@empty
										<tr>
											<td colspan="5" class="text-center">No results found.</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
						Ditampilkan 1 - 1 dari 1 Data
					</div>
				</div>
			</div>

			@php
				$dataSubjek = App\Models\DataSubjek::where('id_dokumen', $data->id)->get();
			@endphp
			{{-- Tab data subjek --}}
			<div class="tab-pane tab-item" data-tab="dataSubjek" id="tab_3">
				<div class="box-header">
					<a class="btn btn-success btn-flat" href="{{ route('backend.form_subjek.create', $data->id) }}">
						<i class="fa fa-plus-circle"></i> Tambah Subjek
					</a>
					<p></p>
					<div id="w5" class="grid-view is-bs3 hide-resize">
						<div id="w5-container" class="table-responsive kv-grid-container">
							<table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
								<thead>
									<tr>
										<th class="text-center" style="width: 60px;">No</th>
										<th>Nama Subjek</th>
										<th style="width: 150px;">Tipe Subjek</th>
										<th style="width: 150px;">Jenis Subjek</th>
										<th class="text-center" style="width: 150px;">Aksi</th>
									</tr>
								</thead>
								<tbody>
									@forelse ($dataSubjek as $item)
										<tr>
											<td class="text-center">{{ $loop->iteration }}.</td>
											<td>{{ $item->subyek }}</td>
											<td>{{ $item->tipe_subyek }}</td>
											<td>{{ $item->jenis_subyek }}</td>
											<td class="text-center">
												<a href="{{ route('backend.form_subjek.edit', [$data->id, $item->id]) }}"
													class="btn btn-sm btn-warning">
													<b class="fa fa-pencil"></b>
												</a>&nbsp;

												<form style="display: inline" action="{{ route('backend.form_subjek.destroy', [$data->id, $item->id]) }}"
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
										</tr>
									@empty
										<tr>
											<td colspan="5" class="text-center">No results found.</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
						Ditampilkan 1 - 1 dari 1 Data
					</div>
				</div>
			</div>
		</div>

</x-layouts.backend>
