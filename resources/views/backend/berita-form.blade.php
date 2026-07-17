<x-layouts.backend :title="$title" :listNav="[['label' => 'Berita', 'route' => route('backend.berita.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.berita.create') ? true : false;
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal"
			action="{{ $isCreate ? route('backend.berita.store') : route('backend.berita.update', $data->id) }}"
			method="POST" enctype="multipart/form-data">
			@csrf
			@if (!$isCreate)
				@method('PATCH')
			@endif

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>
				<div class="box-body">

					<x-backend.input.date
						label="Tanggal"
						key="tanggal"
						:value="$data->tanggal ?? ''"
						required
						placeholder="Tulis tanggal berita" />

					<x-backend.input.text
						label="Judul"
						key="judul"
						:value="$data->judul ?? ''"
						required />

					<x-backend.input.editor
						label="Isi"
						key="isi"
						:value="$data->isi ?? ''"
						required
						placeholder="Tulis isi pengumuman..." />

					<x-backend.input.file
						label="Sampul Berita"
						key="image"
						:value="$data->image ?? ''"
						:mimes="['jpg', 'jpeg', 'png']" />

					<x-backend.input.select
						label="Status"
						key="status"
						:value="$data->status ?? ''"
						placeholder="Pilih Status..."
						required
						:data="[['label' => 'Publish', 'value' => 1], ['label' => 'Tidak Publish', 'value' => 0]]" />

				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>
					<a class="btn btn-danger btn-flat" href="{{ route('backend.berita.index') }}">
						<i class="fa fa-remove"></i> Batal</a>
				</div>
			</div>
		</form>
	</div>
</x-layouts.backend>
