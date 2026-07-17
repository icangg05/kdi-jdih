<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isRouteCreatePengarang = request()->routeIs('backend.form_teu.create.pengarang') ? true : false;
		$route = $isRouteCreatePengarang
		    ? route('backend.form_teu.store.pengarang', $idDokumen)
		    : route('backend.form_teu.store', $idDokumen);
	@endphp


	<div class="box-body no-padding">
		<form class="form-horizontal" action="{{ $route }}" method="post">
			@csrf
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>

				<div class="box-body">

					@if ($isRouteCreatePengarang)
						{{-- Nama pengarang baru --}}
						<x-backend.input.text
							label="Nama Pengarang Baru"
							key="nama_pengarang"
							placeholder="Tulis nama pengarang baru"
							required />
					@else
						{{-- Nama pengarang --}}
						<x-backend.input.select
							label="Nama Pengarang"
							key="nama_pengarang"
							placeholder="--Pilih nama pengarang--"
							required
							:data="$pengarang" />

						{{-- Tipe pengarang --}}
						<x-backend.input.select
							label="Tipe Pengarang"
							key="tipe_pengarang"
							placeholder="--Pilih tipe pengarang--"
							required
							:data="$tipePengarang" />

						{{-- Tipe pengarang --}}
						<x-backend.input.select
							label="Tipe Pengarang"
							key="jenis_pengarang"
							placeholder="--Pilih jenis pengarang--"
							required
							:data="$jenisPengarang" />
					@endif

				</div>

				<div class="box-footer">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>&nbsp;

					@if ($isRouteCreatePengarang)
						<a class="btn btn-primary btn-flat" href="{{ route('backend.form_teu.create', $idDokumen) }}">
							<i class="fa fa-arrow-left"></i> kembali
						</a>&nbsp;
					@else
						<a class="btn btn-danger btn-flat" href="{{ url()->previous() }}">
							<i class="fa fa-remove"></i> Batal
						</a>&nbsp;
					@endif

					@if (!$isRouteCreatePengarang)
						<a class="btn btn-primary btn-flat" href="{{ route('backend.form_teu.create.pengarang', $idDokumen) }}">
							<i class="fa fa-plus-circle"></i> Tambah TEU Baru
						</a>
					@endif
				</div>
			</div>

		</form>
	</div>
</x-layouts.backend>
