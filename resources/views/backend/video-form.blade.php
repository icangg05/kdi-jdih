<x-layouts.backend :title="$title" :listNav="[['label' => 'Video', 'route' => route('backend.video.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.video.create') ? true : false;
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal"
			action="{{ $isCreate ? route('backend.video.store') : route('backend.video.update', $data->id) }}"
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
						placeholder="Tulis tanggal video" />

					<x-backend.input.text
						label="Judul"
						key="judul"
						:value="$data->judul ?? ''"
						required />

					<x-backend.input.text
						label="Link"
						key="link"
						:value="$data->link ?? ''"
						required />

				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>
					<a class="btn btn-danger btn-flat" href="{{ route('backend.video.index') }}">
						<i class="fa fa-remove"></i> Batal</a>
				</div>
			</div>
		</form>
	</div>
</x-layouts.backend>
