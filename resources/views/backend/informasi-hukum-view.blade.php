<x-layouts.backend :title="$title" :listNav="[['label' => 'Informasi Hukum', 'route' => route('backend.informasi-hukum.index')], ['label' => $title]]">
	<div class="box-body table-responsive no-padding">
		<div class="box box-primary box-solid">
			<div class="box-header with-border">
				<b>Detail Data Informasi Hukukm</b>
			</div>
			<div class="box-body">
				<div class="box-header">
					<a class="btn btn-success btn-flat" href="{{ route('backend.informasi-hukum.index') }}">
						<i class="fa fa-mail-reply"></i> Kembali
					</a>
					<a class="btn btn-primary btn-flat" href="{{ route('backend.informasi-hukum.edit', $data->id) }}">
						<i class="fa fa-pencil"></i> Ubah
					</a>

					<form style="display: inline" action="{{ route('backend.informasi-hukum.destroy', $data->id) }}"
						method="POST">
						@csrf
						@method('DELETE')
						<button type="submit" style="outline: none" class="btn btn-danger btn-flat"
							onclick="return confirm('Yakin akan menghapus data ini?')">
							<i class="fa fa-trash"></i> Hapus
						</button>
					</form>
					<p></p>
				</div>
				<table class="table table-striped table-bordered detail-view">
					<tr>
						<td colspan="2">
							<x-backend.line-with-title title="Data Utama" />
						</td>
					</tr>
					<tr>
						<th>Jenis Informasi Hukum</th>
						<td>{{ $data->jenis }}</td>
					</tr>
					<tr>
						<th>Tanggal</th>
						<td>
							{{ Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}</td>
					</tr>
					<tr>
						<th>Judul</th>
						<td>
							{{ $data->judul }}</td>
					</tr>
					<tr>
						<th>Isi</th>
						<td>
							{!! $data->isi !!}
						</td>
					</tr>
					<tr>
						<th class="text-nowrap">Gambar</th>
						@php
							$image = checkFilePath(config('app.img_directory'), $data->image)
							    ? 'storage/' . config('app.img_directory') . $data->image
							    : config('app.default_img');
						@endphp
						<td>
							<img style="border: 1px solid #cccccc" src="{{ asset($image) }}" width="200" alt="image.jpg">
						</td>
					</tr>
					<tr>
						<th>Dokumen</th>
						<td>
							@if (checkFilePath(config('app.doc_directory'), $data->dokumen))
								<form action="{{ route('download_file') }}" method="POST">
									@csrf
									<input type="hidden" name="filePath"
										value="{{ config('app.doc_directory') . $data->dokumen }}">
									<button type="submit" class="btn btn-primary btn-sm badge">Download</button>
								</form>
							@else
								<button disabled class="btn btn-primary btn-sm badge" style="opacity: .5">Download</a>
							@endif
						</td>
					</tr>
					<tr>
						<th>Status</th>
						<td>
							<span @class([
								'btn',
								'btn-sm',
								'badge',
								'btn-success' => $data->status == 1,
								'btn-danger' => $data->status == 0,
							])>
								{{ $data->status == 1 ? 'Publish' : 'Tidak Publish' }}
							</span>
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
							{{ Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i:s') }}</td>
					</tr>
					<tr>
						<th>Created By</th>
						<td>{{ ucfirst($data->created_by) }}</td>
					</tr>
					<tr>
						<th>Updated At</th>
						<td>
							{{ Carbon\Carbon::parse($data->updated_at)->translatedFormat('d F Y, H:i:s') }}</td>
					</tr>
					<tr>
						<th>Updated By</th>
						<td>{{ ucfirst($data->updated_by) }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</x-layouts.backend>
