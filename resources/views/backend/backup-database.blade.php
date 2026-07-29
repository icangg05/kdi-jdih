<x-layouts.backend title="Backup Database" :listNav="[['label' => 'Backup Database']]">

	<div class="row">
		<div class="col-md-8">
			@if (session('error'))
				<div class="alert alert-danger">
					<i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
				</div>
			@endif

			<div class="box box-danger box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-database"></i> Backup Database</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed" style="margin-bottom:15px">
						<tr>
							<th width="35%">Nama Database</th>
							<td><code>{{ $database }}</code></td>
						</tr>
						<tr>
							<th>Ukuran</th>
							<td><b>{{ $size }}</b> <span class="text-muted">(perkiraan, dari information_schema)</span></td>
						</tr>
						<tr>
							<th>Jumlah Tabel</th>
							<td>{{ $tables }}</td>
						</tr>
					</table>

					<p>
						Tombol di bawah mengunduh seluruh isi database sebagai satu berkas <code>.sql</code>.
						Berkas berisi struktur beserta datanya, dan bisa dipakai untuk memulihkan database lewat
						phpMyAdmin atau perintah <code>mysql</code>.
					</p>
					<p class="text-muted" style="margin-bottom:0">
						Berkas hasil unduhan memuat seluruh data situs, termasuk data akun pengguna. Simpan di tempat
						yang aman dan jangan dibagikan.
					</p>
				</div>
				<div class="box-footer">
					<a href="{{ route('backend.backup-database.download') }}" class="btn btn-danger btn-flat"
						id="btn-backup">
						<i class="fa fa-download"></i> Unduh Backup
					</a>
				</div>
			</div>
		</div>
	</div>

	@push('script')
		<script>
			// Unduhan berkas tidak memuat ulang halaman, jadi tidak ada kejadian yang menandai
			// tombol harus pulih. Pakai timer: 8 detik cukup lama untuk terlihat sebagai umpan
			// balik, dan tombol tetap kembali normal kalau unduhan lebih lama dari itu.
			document.getElementById('btn-backup').addEventListener('click', function() {
				var asli = this.innerHTML;

				this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyiapkan berkas...';
				this.style.pointerEvents = 'none';

				setTimeout(function(el) {
					el.innerHTML = asli;
					el.style.pointerEvents = '';
				}, 8000, this);
			});
		</script>
	@endpush

</x-layouts.backend>
