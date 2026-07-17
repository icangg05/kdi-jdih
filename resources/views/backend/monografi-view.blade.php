<x-backend.section-document-view
	:title="$title"
	:data="$data"
	:listNav="[['label' => 'Monografi', 'route' => route('backend.monografi.index')], ['label' => $title]]">

	<x-slot:tabPane>
		<li @class(['active' => session('tabActive') == 'dataEksemplar'])>
			<a
				href="#tab_5"
				data-toggle="tab">Eksemplar</a>
		</li>
	</x-slot:tabPane>

	<div @class(['tab-pane', 'active' => !session('tabActive')]) id="tab_1">
		<div class="box-header">
			<a
				class="btn btn-success btn-flat"
				href="{{ route('backend.monografi.index') }}">
				<i class="fa fa-mail-reply"></i> Kembali
			</a>&nbsp;
			<a
				class="btn btn-primary btn-flat"
				href="{{ route('backend.monografi.edit', $data->id) }}">
				<i class="fa fa-pencil"></i> Ubah Data Monografi
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
					<th>Jenis Monografi</th>
					<td>{{ $data->jenis_peraturan ?? '—' }}</td>
				</tr>
				<tr>
					<th>Judul Monografi</th>
					<td>{{ $data->judul ?? '—' }}</td>
				</tr>
				<tr>
					<th>Tahun</th>
					<td>{{ $data->tahun_terbit ?? '—' }}</td>
				</tr>
				<tr>
					<th>Penerbit</th>
					<td>{{ $data->penerbit ?? '—' }}</td>
				</tr>
				<tr>
					<th>Tempat Terbit</th>
					<td>{{ $data->tempat_terbit ?? '—' }}</td>
				</tr>
				<tr>
					<th>ISBN</th>
					<td>{{ !empty($data->isbn) ? $data->isbn : '—' }}</td>
				</tr>
				<tr>
					<th>Nomor Panggil</th>
					<td>{{ !empty($data->nomor_panggil) ? $data->nomor_panggil : '—' }}</td>
				</tr>
				<tr>
					<th>Deskripsi Fisik</th>
					<td>{{ !empty($data->deskripsi_fisik) ? $data->deskripsi_fisik : '—' }}</td>
				</tr>
				<tr>
					<th>Klasifikasi</th>
					<td>{{ !empty($data->klasifikasi) ? $data->klasifikasi : '—' }}</td>
				</tr>
				<tr>
					<th>Anotasi</th>
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
					<th>Gambar Sampul</th>
					<td>
						@php
							$image = checkFilePath(config('app.img_directory'), $data->gambar_sampul)
							    ? asset('storage/' . config('app.img_directory') . "$data->gambar_sampul")
							    : asset('assets/img/default-book.png');
						@endphp
						<img src="{{ $image }}" alt="sampul.jpg" style="width: 150px">
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
