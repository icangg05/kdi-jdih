@php
	$prefixRoute = 'putusan';
	$isCreate = request()->routeIs("backend.$prefixRoute.create") ? true : false;
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
							$selectJenisPutusan = DB::table('document_type')
							    ->where('second_id', 4)
							    ->pluck('name')
							    ->map(fn($name) => ['label' => $name, 'value' => $name]);
						@endphp
						{{-- Jenis --}}
						<x-backend.input.select
							label="Jenis Putusan"
							key="jenis_peraturan"
							placeholder="--Pilih jenis putusan--"
							required
							:value="$data->jenis_peraturan ?? ''"
							:data="$selectJenisPutusan" />

						{{-- Judul --}}
						<x-backend.input.textarea
							label="Judul"
							key="judul"
							placeholder="Tulis lengkap judul putusan"
							required
							:value="$data->judul ?? ''" />

						{{-- Nomor --}}
						<x-backend.input.text
							label="Nomor Putusan"
							key="nomor_peraturan"
							placeholder="Tulis nomor putusan"
							required
							:value="$data->nomor_peraturan ?? ''" />

						{{-- Pemohon --}}
						<x-backend.input.text
							label="Pemohon"
							key="pemohon"
							placeholder="Tulis nama pemohon"
							:value="$data->pemohon ?? ''" />

						{{-- Termohon --}}
						<x-backend.input.text
							label="Termohon"
							key="termohon"
							placeholder="Tulis nama termohon"
							:value="$data->termohon ?? ''" />

						{{-- lembaga peradilan --}}
						<x-backend.input.text
							label="Lembaga Peradilan"
							key="lembaga_peradilan"
							placeholder="Tulis tempat lembaga peradilan"
							:value="$data->lembaga_peradilan ?? ''" />

						{{-- Tahun --}}
						<x-backend.input.text
							label="Tahun"
							key="tahun_terbit"
							placeholder="Tulis tahun putusan"
							type="number"
							required
							:value="$data->tahun_terbit ?? ''" />

						@php
							$selectTempatTerbit = Storage::exists('data_wilayah.json')
							    ? json_decode(Storage::get('data_wilayah.json'), true)
							    : [];
						@endphp
						{{-- Tempat terbit --}}
						<x-backend.input.select
							label="Tempat Terbit"
							key="tempat_terbit"
							placeholder="--Pilih tempat terbit--"
							:value="$data->tempat_terbit ?? ''"
							:data="$selectTempatTerbit" />

						{{-- Tanggal --}}
						<x-backend.input.date
							label="Tanggal Putusan"
							key="tanggal_penetapan"
							placeholder="Tulis tanggal putusan"
							required
							:value="$data->tanggal_penetapan ?? ''" />

						{{-- Penandatanganan --}}
						<x-backend.input.text
							label="Penandatanganan"
							key="penandatanganan"
							placeholder="Tulis penandatanganan"
							:value="$data->penandatanganan ?? ''" />

						{{-- Klasifikasi --}}
						<x-backend.input.textarea
							label="Klasifikasi"
							key="klasifikasi"
							placeholder="Tulis klasifikasi"
							:value="$data->klasifikasi ?? ''" />

						{{-- Tingkat proses --}}
						<x-backend.input.text
							label="Tingkat Proses"
							key="sub_klasifikasi"
							placeholder="Tulis tingkat proses"
							:value="$data->sub_klasifikasi ?? ''" />

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

						{{-- Amar putusan --}}
						<x-backend.input.textarea
							label="Amar Putusan"
							key="amar_status"
							placeholder="Tulis amar_putusan"
							:value="$data->amar_status ?? ''" />

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
					href="{{ route('backend.putusan.index') }}"
					class="btn btn-danger btn-flat">Batal</a>
			</form>
		</div>
	</div>
</x-layouts.backend>
