<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isCreate = request()->routeIs('backend.form-peraturan-terkait.create', $idDokumen) ? true : false;
		$route = $isCreate
		    ? route('backend.form-peraturan-terkait.store', $idDokumen)
		    : route('backend.form-peraturan-terkait.update', [$idDokumen, $peraturanTerkait->id]);
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal" action="{{ $route }}" method="post">
			@csrf
			@if (!$isCreate)
				@method('patch')
			@endif

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>

				<div class="box-body">
					{{-- Status --}}
					<x-backend.input.select
						label="Status"
						key="status_perter"
						placeholder="--Pilih status--"
						:value="$peraturanTerkait->status_perter ?? ''"
						required
						:data="$dataStatus" />

					{{-- Peraturan terkait --}}
					<x-backend.input.select
						label="Peraturan Terkait"
						key="peraturan_terkait"
						:value="$peraturanTerkait->peraturan_terkait ?? ''"
						placeholder="--Pilih peraturan--"
						required
						:data="$dataPeraturan" />

					{{-- Catatan --}}
					<x-backend.input.text
						label="Catatan"
						key="catatan_perter"
						:value="$peraturanTerkait->catatan_perter ?? ''"
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
