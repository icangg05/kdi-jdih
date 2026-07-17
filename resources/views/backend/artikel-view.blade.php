<x-backend.section-document-view
	:title="$title"
	:data="$data"
	:listNav="[['label' => 'Artikel', 'route' => route('backend.artikel.index')], ['label' => $title]]">

	<div class="tab-pane tab-item" data-tab="dataUtama" id="tab_1">
		<div class="box-header">
			<a
				class="btn btn-success btn-flat"
				href="{{ route('backend.artikel.index') }}">
				<i class="fa fa-mail-reply"></i> Kembali
			</a>&nbsp;
			<a
				class="btn btn-primary btn-flat"
				href="{{ route('backend.artikel.edit', $data->id) }}">
				<i class="fa fa-pencil"></i> Ubah Data Artikel
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
					<th>Jenis Artikel</th>
					<td>{{ $data->jenis_peraturan ?? '—' }}</td>
				</tr>
				<tr>
					<th>Tahun</th>
					<td>{{ $data->tahun_terbit ?? '—' }}</td>
				</tr>
				<tr>
					<th>Judul Artikel</th>
					<td>{{ $data->judul ?? '—' }}</td>
				</tr>
				<tr>
					<th>Tanggal Artikel</th>
					<td>{{ Carbon\Carbon::parse($data->tanggal_penetapan)->translatedFormat('d F Y') ?? '—' }}</td>
				</tr>
				<tr>
					<th>Sumber</th>
					<td>{{ !empty($data->sumber) ? $data->sumber : '—' }}</td>
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

</x-backend.section-document-view>
