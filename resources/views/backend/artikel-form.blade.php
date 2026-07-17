@php
	$prefixRoute = 'artikel';
	$isCreate    = request()->routeIs("backend.$prefixRoute.create") ? true : false;
@endphp

<x-layouts.backend
	:title="$title"
	:listNav="[['label' => ucfirst($prefixRoute), 'route' => route('backend.' . $prefixRoute . '.index')], ['label' => $title]]">

	<div class="box-body no-padding">
		<div class="section">
			<form
				class="form-horizontal"
				action="{{ $isCreate ? route("backend.$prefixRoute.store") : route("backend.$prefixRoute.update", $data->id) }}"
				method="POST"
				enctype="multipart/form-data">
				@csrf
				@if (!$isCreate)
					@method('PATCH')
				@endif

				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>Form Data Utama</b>
					</div>
					<div class="box-body">
						@php
							$selectJenisArtikel = DB::table('document_type')
							    ->where('second_id', 3)
							    ->pluck('name')
							    ->map(fn($name) => ['label' => $name, 'value' => $name]);
						@endphp
						{{-- Jenis --}}
						<x-backend.input.select
							label="Jenis Artikel"
							key="jenis_peraturan"
							placeholder="--Pilih jenis artikel--"
							required
							:value="$data->jenis_peraturan ?? ''"
							:data="$selectJenisArtikel" />

						{{-- Judul --}}
						<x-backend.input.textarea
							label="Judul"
							key="judul"
							placeholder="Tulis lengkap judul artikel"
							required
							:value="$data->judul ?? ''" />

						{{-- Tahun terbit --}}
						<x-backend.input.text
							label="Tahun"
							key="tahun_terbit"
							placeholder="Tulis tahun artikel"
							required
							type="number"
							:value="$data->tahun_terbit ?? ''" />

						{{-- Tanggal --}}
						<x-backend.input.date
							label="Tanggal Artikel"
							key="tanggal_penetapan"
							placeholder="Tulis tanggal artikel"
							required
							:value="$data->tanggal_penetapan ?? ''" />

						{{-- Sumber --}}
						<x-backend.input.textarea
							label="Sumber"
							key="sumber"
							placeholder="Tulis sumber"
              required
							:value="$data->sumber ?? ''" />

						@php
							$selectBahasa = DB::table('bahasa')->pluck('name')->map(fn($name) => ['label' => $name, 'value' => $name]);
						@endphp
						{{-- Bahasa --}}
						<x-backend.input.select
							label="Bahasa"
							key="bahasa"
							placeholder="--Silahkan pilih--"
							:value="$data->bahasa ?? ''"
              required
							:data="$selectBahasa" />

						@php
							$selectBidangHukum = DB::table('bidang_hukum')
							    ->get()
							    ->map(fn($item) => ['label' => $item->name, 'value' => $item->name]);
						@endphp
						{{-- Bidang hukum --}}
						<x-backend.input.select
							label="Bidang Hukum"
							key="bidang_hukum"
							placeholder="--Silahkan pilih--"
							:value="$data->bidang_hukum ?? ''"
							:data="$selectBidangHukum" />

						{{-- Abstrak --}}
						<x-backend.input.file-small
							label="Abstrak"
							key="abstrak"
							:value="$data->abstrak ?? ''"
							:mimes="['pdf']" />
					</div>
				</div>

				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<b>Form Data Dokumen</b>
					</div>
					<div class="box-body">
						{{-- Judul lampiran --}}
						<x-backend.input.text
							label="Judul Lampiran"
							key="judul_lampiran"
							placeholder="Tulis judul lampiran"
							:value="$lampiran->judul_lampiran ?? ''" />

						{{-- Judul --}}
						<x-backend.input.textarea
							label="Deskripsi Lampiran"
							key="deskripsi_lampiran"
							rows="2"
							placeholder="Tulis deskripsi lampiran"
							:value="$lampiran->deskripsi_lampiran ?? ''" />

						{{-- Dokumen Lampiran --}}
						<x-backend.input.file-small
							label="Dokumen Lampiran"
							key="dokumen_lampiran"
							:value="$lampiran->dokumen_lampiran ?? ''"
							:mimes="['pdf']" />

						@push('script')
							<script>
								let dokumenLampiran = @json($lampiran->dokumen_lampiran ?? '');

								$(document).ready(function() {
									function toggleRequiredFields() {
										let judul = $('#judul_lampiran').val().trim();
										let dokumen = $('#dokumen_lampiran').val().trim();
										let deskripsi = $('#deskripsi_lampiran').val().trim();

										// Reset semua required
										$('#judul_lampiran').removeAttr('required');
										$('#dokumen_lampiran').removeAttr('required');

										if (dokumenLampiran)
											$('#judul_lampiran').attr('required', true);

										// Aturan: Jika deskripsi atau dokumen diisi → judul wajib
										if (deskripsi !== '' || dokumen !== '') {
											$('#judul_lampiran').attr('required', true);
										}

										// Aturan: Jika judul diisi → dokumen wajib
										if (!dokumenLampiran) {
											if (judul !== '') {
												$('#dokumen_lampiran').attr('required', true);
											}
										}
									}

									// Trigger saat halaman load dan saat berubah
									toggleRequiredFields();

									$('#judul_lampiran, #dokumen_lampiran, #deskripsi_lampiran').on('input change', function() {
										toggleRequiredFields();
									});
								});
							</script>
						@endpush
					</div>
				</div>

				<button
					type="submit"
					class="btn btn-success btn-flat">Simpan</button>
				<a
					href="{{ route('backend.monografi.index') }}"
					class="btn btn-danger btn-flat">Batal</a>
			</form>
		</div>
	</div>
</x-layouts.backend>
