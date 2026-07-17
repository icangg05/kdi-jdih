<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isCreate = request()->routeIs('backend.form-hasil-uji-materi.create', $idDokumen) ? true : false;
		$route = $isCreate
		    ? route('backend.form-hasil-uji-materi.store', $idDokumen)
		    : route('backend.form-hasil-uji-materi.update', [$idDokumen, $hasilUjiMateri->id]);

		$required = $isCreate || !checkFilePath(config('app.doc_directory'), $hasilUjiMateri->hasil_uji_materi);
		
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal" action="{{ $route }}" method="post" enctype="multipart/form-data">
			@csrf
			@if (!$isCreate)
				@method('patch')
			@endif

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>

				<div class="box-body">
					{{-- File hasil uji materi --}}
					<x-backend.input.file-small
						label="Hasil Uji Materi"
						key="hasil_uji_materi"
						:value="$hasilUjiMateri->hasil_uji_materi ?? ''"
						:mimes="['pdf']"
						:required="$required" />

					{{-- Status hasil uji materi --}}
					<x-backend.input.text
						label="Status Hasil Uji Materi"
						key="status_hasum"
						:value="$hasilUjiMateri->status_hasum ?? ''"
						placeholder="Tulis catatan peraturan"
						required />

					{{-- Catatan hasil uji materi --}}
					<x-backend.input.text
						label="Catatan Hasil Uji Materi"
						key="catatan_hasum"
						:value="$hasilUjiMateri->catatan_hasum ?? ''"
						placeholder="Tulis catatan peraturan" />
				</div>

				<div class="box-footer">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>&nbsp;
					<a class="btn btn-danger btn-flat" href="{{ url()->previous() }}">
						<i class="fa fa-remove"></i> Batal
					</a>&nbsp;
				</div>
			</div>

		</form>
	</div>
</x-layouts.backend>
