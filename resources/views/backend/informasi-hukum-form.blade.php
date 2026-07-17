<x-layouts.backend :title="$title" :listNav="[['label' => 'Informasi Hukum', 'route' => route('backend.informasi-hukum.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.informasi-hukum.create') ? true : false;
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal"
			action="{{ $isCreate ? route('backend.informasi-hukum.store') : route('backend.informasi-hukum.update', $data->id) }}"
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

					@php
						$selectJenisInfokum = DB::table('jenis_informasi_hukum')
						    ->get()
						    ->map(fn($item) => ['label' => "$item->name — $item->singkatan", 'value' => $item->id]);
					@endphp
					<x-backend.input.select
						label="Jenis Informasi Hukum"
						key="jenis"
						:value="$data->jenis ?? ''"
						placeholder="--Pilih jenis informasi hukum--"
						required
						:data="$selectJenisInfokum" />

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
						label="Sampul"
						key="image"
						:value="$data->image ?? ''"
						:mimes="['jpg', 'jpeg', 'png']" />

					<x-backend.input.file
						label="Dokumen"
						key="dokumen"
						:value="$data->dokumen ?? ''"
						:mimes="['pdf']" />

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
					<a class="btn btn-danger btn-flat" href="{{ route('backend.informasi-hukum.index') }}">
						<i class="fa fa-remove"></i> Batal</a>
				</div>
			</div>
		</form>
	</div>
</x-layouts.backend>
