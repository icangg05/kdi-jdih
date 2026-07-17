<x-layouts.backend :title="$title" :listNav="[['label' => 'Video', 'route' => route('backend.video.index')], ['label' => $title]]">
	<div class="box-body table-responsive no-padding">
		<div class="box box-primary box-solid">
			<div class="box-header with-border">
				<b>Detail Data Video</b>
			</div>
			<div class="box-body">
				<div class="box-header">
					<a class="btn btn-success btn-flat" href="{{ route('backend.video.index') }}">
						<i class="fa fa-mail-reply"></i> Kembali
					</a>
					<a class="btn btn-primary btn-flat" href="{{ route('backend.video.edit', $data->id) }}">
						<i class="fa fa-pencil"></i> Ubah
					</a>

					<form style="display: inline" action="{{ route('backend.video.destroy', $data->id) }}"
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
						<th>Link</th>
						<td>
							{{ $data->link }}
						</td>
					</tr>
					<tr>
						<th>Preview Video</th>
						<td>
							<iframe width="350" height="230"
								src="https://www.youtube.com/embed/{{ $data->link }}"
								title="YouTube video preview"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowfullscreen>
							</iframe>
						</td>
					</tr>
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
