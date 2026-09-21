<x-layouts.backend :title="$title" :listNav="[
    ['label' => 'Peraturan', 'route' => route('backend.peraturan.index')],
    ['label' => 'Detail', 'route' => route('backend.peraturan.show', $idDokumen)],
    ['label' => $title],
]">
	@php
		$isCreate = request()->routeIs('backend.form-peraturan-pelaksana.create', $idDokumen) ? true : false;
		$route = $isCreate
		    ? route('backend.form-peraturan-pelaksana.store', $idDokumen)
		    : route('backend.form-peraturan-pelaksana.update', [$idDokumen, $peraturanPelaksana->id]);

		// Baris lama tanpa id peraturan berarti isinya dokumen unggahan
		$sumber = old('sumber', !$isCreate && blank($peraturanPelaksana->peraturan_pelaksana) ? 'upload' : 'pilih');
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
					{{-- Sumber peraturan pelaksana --}}
					<div class="form-group @error('sumber') has-error @enderror">
						<label class="control-label col-sm-2">
							Sumber <span style="color: red">*</span>
						</label>
						<div class="col-sm-8">
							<label class="radio-inline">
								<input type="radio" name="sumber" value="pilih" @checked($sumber === 'pilih')> Pilih dari peraturan yang ada
							</label>
							<label class="radio-inline">
								<input type="radio" name="sumber" value="upload" @checked($sumber === 'upload')> Upload dokumen baru
							</label>
							@error('sumber')
								<p class="help-block help-block-error">{{ $message }}</p>
							@enderror
						</div>
					</div>

					{{-- Sumber: pilih peraturan yang sudah ada --}}
					<div class="sumber-pane" data-sumber="pilih">
						<x-backend.input.select
							label="Peraturan Pelaksana"
							key="peraturan_pelaksana"
							:value="$peraturanPelaksana->peraturan_pelaksana ?? ''"
							placeholder="--Pilih peraturan--"
							:data="$dataPeraturan" />
					</div>

					{{-- Sumber: unggah dokumen baru --}}
					<div class="sumber-pane" data-sumber="upload">
						<x-backend.input.text
							label="Judul Peraturan"
							key="judul_pelaksana"
							:value="$peraturanPelaksana->judul_pelaksana ?? ''"
							placeholder="Contoh: Peraturan Wali Kota Kendari Nomor 12 Tahun 2024 tentang ..." />

						<x-backend.input.file-small
							label="Dokumen (PDF)"
							key="file_pelaksana"
							:value="$peraturanPelaksana->file_pelaksana ?? ''"
							:mimes="['pdf']" />
					</div>

					{{-- Catatan --}}
					<x-backend.input.text
						label="Catatan"
						key="catatan_pelaksana"
						:value="$peraturanPelaksana->catatan_pelaksana ?? ''"
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

	@push('script')
		<script>
			// Didaftarkan setelah script komponen, jadi select2 sudah terpasang saat panel disembunyikan
			$(function() {
				function tampilkanSumber() {
					var sumber = $('input[name="sumber"]:checked').val();
					$('.sumber-pane').each(function() {
						$(this).toggle($(this).data('sumber') === sumber);
					});
				}

				$('input[name="sumber"]').on('change', tampilkanSumber);
				tampilkanSumber();
			});
		</script>
	@endpush
</x-layouts.backend>
