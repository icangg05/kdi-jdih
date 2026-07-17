<x-backend.section-document-view
	:title="$title"
	:data="$data"
	:listNav="[['label' => 'Putusan', 'route' => route('backend.putusan.index')], ['label' => $title]]">

	<x-slot:tabPane>
		<li class="tab-item" data-tab="dataPeraturanTerkait"
			onclick="localStorage.setItem('tabActive', 'dataPeraturanTerkait');">
			<a
				href="#tab_5"
				data-toggle="tab">Peraturan Terkait</a>
		</li>
	</x-slot:tabPane>

	<div class="tab-pane tab-item" data-tab="dataUtama" id="tab_1">
		<div class="box-header">
			<a
				class="btn btn-success btn-flat"
				href="{{ route('backend.putusan.index') }}">
				<i class="fa fa-mail-reply"></i> Kembali
			</a>&nbsp;
			<a
				class="btn btn-primary btn-flat"
				href="{{ route('backend.putusan.edit', $data->id) }}">
				<i class="fa fa-pencil"></i> Ubah Data Putusan
			</a>
			<p></p>
			<table
				id="w0"
				class="table table-striped table-bordered detail-view">
				<tr>
					<td colspan="2">
						<x-backend.line-with-title title="Data Utama" />
					</td>
				</tr>
				<tr>
					<th>Jenis Putusan</th>
					<td>{{ $data->jenis_peraturan ?? '—' }}</td>
				</tr>
				<tr>
					<th>Judul</th>
					<td>{{ !empty($data->judul) ? $data->judul : '—' }}</td>
				</tr>
				<tr>
					<th>Nomor</th>
					<td>{{ !empty($data->nomor_peraturan) ? $data->nomor_peraturan : '—' }}</td>
				</tr>
				<tr>
					<th>Pemohon</th>
					<td>{{ !empty($data->pemohon) ? $data->pemohon : '—' }}</td>
				</tr>
				<tr>
					<th>Termohon</th>
					<td>{{ !empty($data->termohon) ? $data->termohon : '—' }}</td>
				</tr>
				<tr>
					<th>Lembaga Peradilan</th>
					<td>{{ !empty($data->lembaga_peradilan) ? $data->lembaga_peradilan : '—' }}</td>
				</tr>
				<tr>
					<th>Tahun</th>
					<td>{{ !empty($data->tahun_terbit) ? $data->tahun_terbit : '—' }}</td>
				</tr>
				<tr>
					<th>Tempat Terbit</th>
					<td>{{ !empty($data->tempat_terbit) ? $data->tempat_terbit : '—' }}</td>
				</tr>
				<tr>
					<th>Tanggal Putusan</th>
					<td>{{ Carbon\Carbon::parse($data->tanggal_penetapan)->translatedFormat('d F Y') ?? '—' }}</td>
				</tr>
				<tr>
					<th>Penandatanganan</th>
					<td>{{ !empty($data->penandatanganan) ? $data->penandatanganan : '—' }}</td>
				</tr>
				<tr>
					<th>Klasifikasi</th>
					<td>{{ !empty($data->klasifikasi) ? $data->klasifikasi : '—' }}</td>
				</tr>
				<tr>
					<th>Tingkat proses</th>
					<td>{{ !empty($data->sub_klasifikasi) ? $data->sub_klasifikasi : '—' }}</td>
				</tr>
				<tr>
					<th>Bahasa</th>
					<td>{{ $data->bahasa ?? '—' }}</td>
				</tr>
				<tr>
					<th>Bidang Hukum</th>
					<td>{{ $data->bidang_hukum ?? '—' }}</td>
				</tr>
				<tr>
					<th>Dokumen Abstrak</th>
					<td>
						<form
							action="{{ route('download_file') }}"
							method="post">
							@csrf
							<input
								type="hidden"
								name="filePath"
								value="{{ config('app.doc_directory') . $data->abstrak ?? '' }}">
							<button
								@disabled(!checkFilePath(config('app.doc_directory'), $data->abstrak ?? ''))
								type="submit"
								class="btn btn-primary btn-sm badge">
								Download Abstrak
							</button>
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<x-backend.line-with-title title="Data Dokumen" />
					</td>
				</tr>
				<tr>
					<th>Judul Lampiran</th>
					<td>{{ $data->judul_lampiran ?? '—' }}</td>
				</tr>
				<tr>
					<th>Deskripsi Lampiran</th>
					<td>{{ $data->deskripsi_lampiran ?? '—' }}</td>
				</tr>
				<tr>
					<th>Dokumen Lampiran</th>
					<td>
						<form
							action="{{ route('download_file') }}"
							method="post">
							@csrf
							<input
								type="hidden"
								name="filePath"
								value="{{ config('app.doc_directory') . $data->dokumen_lampiran ?? '' }}">
							<button
								@disabled(!checkFilePath(config('app.doc_directory'), $data->dokumen_lampiran ?? ''))
								type="submit"
								class="btn btn-primary btn-sm badge">
								Download Lampiran
							</button>
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<x-backend.line-with-title title="Data Created" />
					</td>
				</tr>
				<tr>
					<th>Created At</th>
					<td>
						{{ Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i:s') }}
					</td>
				</tr>
				<tr>
					<th>Created By</th>
					<td>{{ ucfirst($data->_created_by ?? '—') }}</td>
				</tr>
				<tr>
					<th>Updated At</th>
					<td>
						{{ Carbon\Carbon::parse($data->updated_at)->translatedFormat('d F Y, H:i:s') }}
					</td>
				</tr>
				<tr>
					<th>Updated By</th>
					<td>{{ ucfirst($data->_updated_by ?? '—') }}</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="tab-pane tab-item" data-tab="dataPeraturanTerkait" id="tab_5">
		<div class="box-header">
			<a class="btn btn-success btn-flat"
				href="{{ route('backend.form-peraturan-terkait.create', $data->id) }}">
				<i class="fa fa-plus-circle"></i> Tambah Peraturan Terkait
			</a>
			<p></p>
			<div id="w9" class="grid-view is-bs3 hide-resize">
				<div id="w9-container" class="table-responsive kv-grid-container">
					<table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
						<thead>
							<tr>
								<th class="text-center" style="width: 60px;">No</th>
								<th>Peraturan Terkait</th>
								<th style="width: 150px;">Status</th>
								<th style="min-width: 150px;">Catatan</th>
								<th class="text-center" style="width: 150px;">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($dataPeraturanTerkait as $item)
								<tr>
									<td class="text-center">{{ $loop->iteration }}.</td>
									<td>
										<a
											href="{{ route('backend.peraturan.show', $item->peraturan_terkait) }}">{{ $item->judul_peraturan_terkait }}</a>
									</td>
									<td>{{ $item->status_perter }}</td>
									<td>{{ !empty($item->catatan_perter) ? $item->catatan_perter : '—' }}</td>
									<td class="text-center">
										<a href="{{ route('backend.form-peraturan-terkait.edit', [$data->id, $item->id]) }}"
											class="btn btn-sm btn-warning">
											<b class="fa fa-pencil"></b>
										</a>&nbsp;

										<form style="display: inline"
											action="{{ route('backend.form-peraturan-terkait.destroy', [$data->id, $item->id]) }}"
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
									<td colspan="5">
										<div class="empty text-center">No results found.</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</x-backend.section-document-view>
