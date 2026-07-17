<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isCreate = request()->routeIs('backend.form_subjek.create') ? true : false;
		$routeForm = $isCreate
		    ? route('backend.form_subjek.store', $idDokumen)
		    : route('backend.form_subjek.update', [$idDokumen, $subjek->id]);
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal" action="{{ $routeForm }}" method="post">
			@csrf
			@if (!$isCreate)
				@method('PATCH')
			@endif
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>

				<div class="box-body">
					{{-- Nama subjek --}}
					<x-backend.input.text
						label="Subjek"
						key="subyek"
						:value="$subjek->subyek ?? ''"
						placeholder="Tulis Subjek"
						required />

					{{-- Tipe subjek --}}
					<x-backend.input.select
						label="Tipe Subjek"
						key="tipe_subyek"
						:value="$subjek->tipe_subyek ?? ''"
						placeholder="--Pilih tipe subjek--"
						required
						:data="$tipeKataKunci" />

					{{-- Jenis subjek --}}
					<x-backend.input.select
						label="Jenis Subjek"
						key="jenis_subyek"
						:value="$subjek->jenis_subyek ?? ''"
						placeholder="--Pilih jenis subjek--"
						required
						:data="$jenisSubjek" />
				</div>

				<div class="box-footer">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>&nbsp;
					<a class="btn btn-danger btn-flat" href="{{ url()->previous() }}">
						<i class="fa fa-remove"></i> Batal
					</a>
				</div>
			</div>

		</form>
	</div>
</x-layouts.backend>
