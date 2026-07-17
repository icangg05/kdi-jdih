@php
	$prefixRoute = 'monografi';
	$isCreate = request()->routeIs("backend.$prefixRoute.create") ? true : false;
@endphp

<x-layouts.backend
	:title="$title"
	:listNav="[['label' => 'Monografi', 'route' => route('backend.' . $prefixRoute . '.index')], ['label' => $title]]">

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
							$selectTipeMonografi = DB::table('document_type')
							    ->where('second_id', 2)
							    ->pluck('name')
							    ->map(fn($name) => ['label' => $name, 'value' => $name]);
						@endphp
						{{-- Jenis monografi --}}
						<x-backend.input.select
							label="Jenis Monografi"
							key="jenis_peraturan"
							placeholder="--Pilih jenis monografi--"
							required
							:value="$data->jenis_peraturan ?? ''"
							:data="$selectTipeMonografi" />

						{{-- Judul --}}
						<x-backend.input.textarea
							label="Judul"
							key="judul"
							placeholder="Tulis lengkap judul monografi"
							required
							:value="$data->judul ?? ''" />

						{{-- Tahun terbit --}}
						<x-backend.input.text
							label="Tahun"
							key="tahun_terbit"
							placeholder="Tulis tahun monografi"
							required
							type="number"
							:value="$data->tahun_terbit ?? ''" />

						{{-- Penerbit --}}
						<x-backend.input.text
							label="Penerbit"
							key="penerbit"
							placeholder="Tulis penerbit"
							required
							:value="$data->penerbit ?? ''" />

						@php
							$selectTempatTerbit = Storage::exists('data_wilayah.json')
							    ? json_decode(Storage::get('data_wilayah.json'), true)
							    : [];
						@endphp
						{{-- Tempat terbit --}}
						<x-backend.input.select
							label="Tempat Terbit"
							key="tempat_terbit"
							placeholder="Pilih tempat terbit..."
							required
							:value="$data->tempat_terbit ?? ''"
							:data="$selectTempatTerbit" />

						{{-- Nomor panggil --}}
						<x-backend.input.text
							label="Nomor Panggil"
							key="nomor_panggil"
							placeholder="Tulis nomor panggil"
							type="number"
							:value="$data->nomor_panggil ?? ''" />

						{{-- Deskripsi fisik --}}
						<x-backend.input.text
							label="Deskripsi fisik"
							key="deskripsi_fisik"
							placeholder="Tulis deskripsi fisik"
							:value="$data->deskripsi_fisik ?? ''" />

						{{-- Klasifikasi --}}
						<x-backend.input.text
							label="Klasifikasi"
							key="klasifikasi"
							placeholder="Tulis klasifikasi"
							:value="$data->klasifikasi ?? ''" />

						{{-- Deskripsi fisik --}}
						<x-backend.input.text
							label="ISBN"
							key="isbn"
							placeholder="Tulis ISBN"
							:value="$data->isbn ?? ''" />

						{{-- Anotasi --}}
						<x-backend.input.textarea
							label="Anotasi"
							key="sumber"
							placeholder="Tulis anotasi"
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
							:data="$selectBahasa" />

						@php
							$selectSumberPerolehan = [['label' => 'Beli', 'value' => 'Beli'], ['label' => 'Hibah', 'value' => 'Hibah']];
						@endphp
						{{-- Sumber perolehan --}}
						<x-backend.input.select
							label="Sumber Perolehan"
							key="sumber_perolehan"
							placeholder="--Silahkan pilih--"
							required
							:value="$data->sumber_perolehan ?? ''"
							:data="$selectSumberPerolehan" />

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

						{{-- Gambar sampul --}}
						<x-backend.input.file-small
							label="Sampul"
							key="gambar_sampul"
							:value="$data->gambar_sampul ?? ''"
							:mimes="['jpg', 'jpeg', 'png']" />
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
