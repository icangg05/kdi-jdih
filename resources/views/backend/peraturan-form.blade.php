@php
	$isCreate = request()->routeIs('backend.peraturan.create') ? true : false;
	$title = ($isCreate ? 'Tambah' : 'Edit') . ' Data Peraturan';
@endphp

<x-layouts.backend
	:title="$title"
	:listNav="[['label' => 'Peraturan', 'route' => route('backend.peraturan.index')], ['label' => $title]]">

	<div class="box-body no-padding">
		<div class="section">
			<form
				class="form-horizontal"
				action="{{ $isCreate ? route('backend.peraturan.store') : route('backend.peraturan.update', $peraturan->id) }}"
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
						{{-- Jenis peraturan --}}
						<x-backend.input.select
							label="Jenis Peraturan"
							key="jenis_peraturan"
							placeholder="--Pilih tipe pengelolaan--"
							required
							:value="$peraturan->jenis_peraturan ?? ''"
							:data="$selectTipeDokumen" />
						{{-- Bentuk peraturan --}}
						<x-backend.input.select
							label="Bentuk Peraturan"
							key="bentuk_peraturan"
							placeholder="--Pilih bentuk peraturan--"
							required
							:value="$peraturan->bentuk_peraturan ?? ''"
							:data="[['label' => 'Jenis peraturan belum dipilih', 'value' => '']]" />
						@push('script')
							<script>
								let dataTipeTokumen = @json($dataTipeDokumen);
								let peraturan = @json($peraturan ?? null);

								if (peraturan) {
									$('#jenis_peraturan option').each(function() {
										if ($(this).text().trim() === peraturan.jenis_peraturan) {
											$(this).prop('selected', true);
											return false;
										}
									});

									let dataJenisPeraturan = dataTipeTokumen.find(item => item.name == peraturan.jenis_peraturan);
									let dataBentukPeraturan = dataTipeTokumen.filter(
										item => item.second_id.startsWith(
											`${dataJenisPeraturan.parent_id}:${dataJenisPeraturan.id}:`)
									);
									if (dataBentukPeraturan.length == 0)
										dataBentukPeraturan.push(dataJenisPeraturan);

									$('#bentuk_peraturan').empty();
									dataBentukPeraturan.forEach(function(item) {
										$('#bentuk_peraturan').append(
											`<option value="${item.singkatan}">${item.singkatan}</option>`
										);
									});

									$('#bentuk_peraturan option').each(function() {
										if ($(this).text().trim() === peraturan.bentuk_peraturan) {
											$(this).prop('selected', true);
											return false;
										}
									});
								}


								$(document).ready(function() {
									$('#jenis_peraturan').on('change', function() {
										let jenisPeraturanId = $(this).val();

										if (!jenisPeraturanId) {
											$('#bentuk_peraturan').empty().append(
												`<option value="">--Pilih bentuk peraturan--</option>`
												`<option value="">Jenis peraturan belum dipilih</option>`
											);
											return;
										}
										let dataJenisPeraturan = dataTipeTokumen.find(item => item.id == jenisPeraturanId);
										let dataBentukPeraturan = dataTipeTokumen.filter(
											item => item.second_id.startsWith(
												`${dataJenisPeraturan.parent_id}:${dataJenisPeraturan.id}:`)
										);
										if (dataBentukPeraturan.length == 0)
											dataBentukPeraturan.push(dataJenisPeraturan);

										$('#bentuk_peraturan').empty();
										dataBentukPeraturan.forEach(function(item) {
											$('#bentuk_peraturan').append(
												`<option value="${item.singkatan}">${item.singkatan}</option>`
											);
										});
									})
								});
							</script>
						@endpush

						{{-- Judul --}}
						<x-backend.input.textarea
							label="Judul"
							key="judul"
							placeholder="Tulis lengkap judul peraturan"
							required
							:value="$peraturan->judul ?? ''" />

						{{-- Nomor peraturan --}}
						<x-backend.input.text
							label="Nomor Peraturan"
							key="nomor_peraturan"
							placeholder="Tulis nomor peraturan"
							required
							type="number"
							:value="$peraturan->nomor_peraturan ?? ''" />

						{{-- Tahun terbit --}}
						<x-backend.input.text
							label="Tahun"
							key="tahun_terbit"
							placeholder="Tulis tahun peraturan"
							required
							type="number"
							:value="$peraturan->tahun_terbit ?? ''" />

						{{-- Tempat penetapan --}}
						<x-backend.input.select
							label="Tempat Penetapan"
							key="tempat_terbit"
							placeholder="Pilih tempat penetapan..."
							:value="$peraturan->tempat_terbit ?? ''"
							:data="$selectTempatPenetapan" />

						{{-- Tanggal penetapan --}}
						<x-backend.input.date
							label="Tanggal Penetapan"
							key="tanggal_penetapan"
							placeholder="Tulis tanggal penetapan"
							required
							:value="$peraturan->tanggal_penetapan ?? ''" />

						{{-- Penandatanganan --}}
						<x-backend.input.text
							label="Penandatanganan"
							key="penandatanganan"
							placeholder="Tulis penandatangan peraturan"
							:value="$peraturan->penandatanganan ?? ''" />

						{{-- Tanggal pengundangan --}}
						<x-backend.input.date
							label="Tanggal Pengundangan"
							key="tanggal_pengundangan"
							placeholder="Tulis tanggal pengundangan"
							:value="$peraturan->tanggal_pengundangan ?? ''" />

						{{-- Pemrakarsa --}}
						<x-backend.input.text
							label="Pemrakarsa"
							key="pemrakarsa"
							placeholder="Tulis pemrakarsa"
							:value="$peraturan->pemrakarsa ?? ''" />

						{{-- Sumber --}}
						<x-backend.input.textarea
							label="Sumber"
							key="sumber"
							placeholder="Contoh LN Nomor 21 Tahun 2017"
							:value="$peraturan->sumber ?? ''" />

						{{-- Tempat penetapan --}}
						<x-backend.input.select
							label="Bahasa"
							key="bahasa"
							placeholder="--Silahkan pilih--"
							:value="$peraturan->bahasa ?? ''"
							:data="$selectBahasa" />

						{{-- Urusan pemerintahan --}}
						<x-backend.input.select
							label="Urusan Pemerintahan"
							key="urusan_pemerintahan"
							placeholder="--Silahkan pilih--"
							:value="$peraturan->urusan_pemerintahan ?? ''"
							:data="$selectUrusanPemerintahan" />

						{{-- Bidang hukum --}}
						<x-backend.input.select
							label="Bidang Hukum"
							key="bidang_hukum"
							placeholder="--Silahkan pilih--"
							:value="$peraturan->bidang_hukum ?? ''"
							:data="$selectBidangHukum" />

						{{-- Abstrak --}}
						<x-backend.input.file-small
							label="Abstrak"
							key="abstrak"
							:value="$peraturan->abstrak ?? ''"
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
					href="{{ route('backend.peraturan.index') }}"
					class="btn btn-danger btn-flat">Batal</a>
			</form>
		</div>
	</div>
</x-layouts.backend>
