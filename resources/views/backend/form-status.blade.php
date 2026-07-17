<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isCreate = request()->routeIs('backend.form-status.create', $idDokumen) ? true : false;
		$route = $isCreate
		    ? route('backend.form-status.store', $idDokumen)
		    : route('backend.form-status.update', [$idDokumen, $dataStatus->id]);
	@endphp


	<div id="w1-danger" class="alert-danger alert fade in">
		<i class="icon fa fa-ban"></i>Harap diperhatikan! Perubahan status akan berefek pada peraturan yang dituju dan status
		dari
		peraturan.
	</div>

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
					{{-- Keterangan status --}}
					<x-backend.input.select
						label="Keterangan Status"
						key="status_peraturan"
						:value="$dataStatus->status_peraturan ?? ''"
						placeholder="--Silahkan pilih--"
						:data="$selectStatus"
						required />

					{{-- Judul peraturan --}}
					<x-backend.input.select
						label="Judul Peraturan"
						key="id_dokumen_target"
						:value="$dataStatus->id_dokumen_target ?? ''"
						placeholder="--Pilih peraturan--"
						:data="$selectPeraturan"
						required />
					<input type="hidden" name="id_dokumen_target_old" value="{{ $dataStatus->id_dokumen_target ?? '' }}">

					{{-- Catatan hasil uji materi --}}
					<x-backend.input.text
						label="Catatan Satus Perubahan"
						key="catatan_status_peraturan"
						placeholder="Tulis catatan status peraturan"
						:value="$dataStatus->catatan_status_peraturan ?? ''" />

					{{-- Tanggal perubahan --}}
					<x-backend.input.date
						label="Tanggal Perubahan"
						key="tanggal_perubahan"
						:value="$dataStatus->tanggal_perubahan ?? ''"
						placeholder="Tulis tanggal perubahan"
						required />
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
