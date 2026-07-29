@php
	if ($kategori == 'peraturan') {
	    $columnField = [
	        [__('Tempat Terbit'), 'tempat_terbit'],
	        [__('Tanggal Penetapan'), 'tanggal_penetapan'],
	        [__('Tanggal Pengundangan'), 'tanggal_pengundangan'],
	        [__('Sumber'), 'sumber'],
	        [__('Urusan Pemerintahan'), 'urusan_pemerintahan'],
	        [__('Bidang Hukum'), 'bidang_hukum'],
	        [__('Bahasa'), 'bahasa'],
	        [__('Penandatanganan'), 'penandatanganan'],
	        [__('Pemrakarsa'), 'pemrakarsa'],
	    ];
	} elseif ($kategori == 'monografi') {
	    $columnField = [
	        [__('Nomor Panggil'), 'nomor_panggil'],
	        [__('Penerbit'), 'penerbit'],
	        [__('Tahun Terbit'), 'tahun_terbit'],
	        [__('Deskripsi Fisik'), 'deskripsi_fisik'],
	        [__('Klasifikasi'), 'klasifikasi'],
	        [__('Bahasa'), 'bahasa'],
	        [__('ISBN'), 'isbn'],
	        [__('Tempat Terbit'), 'tempat_terbit'],
	        [__('Anotasi'), 'sumber'],
	        [__('Bidang Hukum'), 'bidang_hukum'],
	    ];
	} elseif ($kategori == 'artikel') {
	    $columnField = [
	        [__('Tahun Terbit'), 'tahun_terbit'],
	        [__('Sumber'), 'sumber'],
	        [__('Bahasa'), 'bahasa'],
	        [__('Bidang Hukum'), 'bidang_hukum'],
	    ];
	} elseif ($kategori == 'putusan') {
	    // Amar Putusan sengaja tidak masuk grid — isinya panjang, dirender di kartu sendiri
	    $columnField = [
	        [__('Klasifikasi'), 'klasifikasi'],
	        [__('Tanggal Dibacakan'), 'tanggal_penetapan'],
	        [__('Tingkat Proses'), 'sub_klasifikasi'],
	        [__('Penggugat / Pemohon'), 'pemohon'],
	        [__('Tergugat / Termohon'), 'termohon'],
	        [__('Tempat Pengadilan'), 'lembaga_peradilan'],
	        [__('Lokasi'), 'tempat_terbit'],
	        [__('Bahasa'), 'bahasa'],
	    ];
	}

	// Kolom yang dirender sebagai tanggal — pakai nama field, bukan indeks,
	// supaya tidak geser saat urutan $columnField berubah
	$dateFields = ['tanggal_penetapan', 'tanggal_pengundangan'];

	$indexUrl = route('frontend.dokumen.index', $kategori);
	$kategoriLabel = Str::words($title, 1, '');

	$lampiranPath = config('app.doc_directory') . $data['dokumen_lampiran'];
	$abstrakPath = config('app.doc_directory') . $data['abstrak'];
	$hasLampiran = checkFilePath(config('app.doc_directory'), $data['dokumen_lampiran']);
	$hasAbstrak = checkFilePath(config('app.doc_directory'), $data['abstrak']);

	$isBerlaku = strtolower((string) $data['status']) === 'berlaku';
@endphp

<div>
	<x-frontend.breadcrumb
		:title="$kategoriLabel . ' ' . __('Detail')"
		:listNav="[
		    ['label' => $kategoriLabel, 'route' => $indexUrl],
		    ['label' => Str::title($data['jenis_peraturan'] ?: __('Detail'))],
		]" />

	<section class="relative bg-linear-to-b from-white to-gray-50 py-10 lg:py-14">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
		</div>

		<div class="relative z-10 max-w-6xl mx-auto px-4 animate-rise">

			{{-- Tombol Kembali --}}
			<a wire:navigate.hover href="{{ $indexUrl }}"
				class="inline-flex items-center gap-2 mb-6 rounded bg-white ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:ring-primary/40 hover:text-primary">
				<i class="fa-solid fa-arrow-left"></i>
				{{ __('Kembali ke') }} {{ $kategoriLabel }}
			</a>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

				{{-- ===== MAIN ===== --}}
				<div class="lg:col-span-2 space-y-6">

					{{-- Header --}}
					<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

						<div class="flex flex-wrap items-center gap-3">
							@if ($data['jenis_peraturan'])
								<span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
									<i class="fa-solid fa-scale-balanced"></i> {{ Str::title($data['jenis_peraturan']) }}
								</span>
							@endif

							@if ($kategori == 'peraturan' && $data['status'])
								<span class="inline-flex items-center gap-1.5 rounded px-3 py-1 text-xs font-semibold
									{{ $isBerlaku ? 'bg-accent/10 text-accent' : 'bg-slate-100 text-slate-500' }}">
									<i class="fa-solid {{ $isBerlaku ? 'fa-circle-check' : 'fa-circle-minus' }}"></i>
									{{ $data['status'] }}
								</span>
							@endif
						</div>

						<h1 class="mt-4 text-lg md:text-xl lg:text-2xl font-bold text-slate-900 leading-snug">
							{{ tt($data, 'judul') }}
						</h1>

						<div class="mt-4 flex flex-wrap items-center gap-4 border-t border-slate-100 pt-4 text-xs text-slate-500">
							<span class="inline-flex items-center gap-1.5">
								<i class="fa-regular fa-eye text-primary/70"></i>
								<span class="font-semibold text-slate-700">{{ number_format($data['hit_see'] ?? 0, 0, ',', '.') }}</span> {{ __('dilihat') }}
							</span>
							<span class="inline-flex items-center gap-1.5">
								<i class="fa-solid fa-cloud-arrow-down text-accent/70"></i>
								<span class="font-semibold text-slate-700">{{ number_format($data['hit_download'] ?? 0, 0, ',', '.') }}</span> {{ __('diunduh') }}
							</span>
						</div>
					</div>

					{{-- Detail Dokumen --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
							<i class="fa-solid fa-circle-info text-primary"></i> {{ __('Detail Dokumen') }}
						</h2>

						<dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
							@foreach ($columnField as $item)
								@php $value = $data[$item[1]] ?? null; @endphp
								<div>
									<dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">{{ $item[0] }}</dt>
									<dd class="mt-0.5 text-sm text-slate-800">
										@if (in_array($item[1], $dateFields))
											{{ $value ? \Carbon\Carbon::parse($value)->translatedFormat('l, j F Y') : '—' }}
										@else
											{{ $value !== null && $value !== '' ? $value : '—' }}
										@endif
									</dd>
								</div>
							@endforeach
						</dl>
					</div>

					{{-- AMAR PUTUSAN (kartu sendiri — isinya panjang) --}}
					@if ($kategori == 'putusan')
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-gavel text-primary"></i> {{ __('Amar Putusan') }}
							</h2>

							@if ($data['amar_status'])
								<div class="mt-5 rounded border-l-2 border-primary bg-slate-50/60 p-5">
									{{-- Data lama tersimpan sebagai teks polos, data baru sebagai HTML dari editor. --}}
									<div class="prose prose-sm prose-slate max-w-none text-slate-700 [&_p]:my-2">
										@if (Str::contains($data['amar_status'], '<'))
											{!! $data['amar_status'] !!}
										@else
											<p class="whitespace-pre-line text-sm leading-relaxed">{{ $data['amar_status'] }}</p>
										@endif
									</div>
								</div>
							@else
								<p class="mt-4 rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center text-sm text-slate-400">
									{{ __('Data Tidak Tersedia') }}
								</p>
							@endif
						</div>
					@endif

					{{-- ===== KHUSUS PERATURAN ===== --}}
					@if ($kategori == 'peraturan')
						{{-- Peraturan Terkait --}}
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-diagram-project text-primary"></i> {{ __('Peraturan Terkait') }}
							</h2>

							<div class="mt-4 space-y-2">
								@forelse ($peraturanTerkait as $item)
									<a wire:navigate.hover
										href="{{ route('frontend.dokumen.show', ['peraturan', Hashids::encode($item->peraturan_terkait)]) }}"
										class="group flex items-start gap-3 rounded border border-slate-200 p-3 transition hover:border-primary/40 hover:bg-primary/5">
										<span class="mt-0.5 shrink-0 rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
											{{ $item->status_perter }}
										</span>
										<span class="text-sm font-medium text-slate-800 transition group-hover:text-primary">
											{{ $item->judul_peraturan_terkait }}
										</span>
									</a>
								@empty
									<p class="rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-6 text-center text-sm text-slate-400">
										{{ __('Data Tidak Tersedia') }}
									</p>
								@endforelse
							</div>
						</div>

						{{-- Dokumen Terkait & Hasil Uji Materi --}}
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							@foreach ([
								['label' => __('Dokumen Terkait'), 'icon' => 'fa-paperclip', 'items' => $dokumenTerkait, 'field' => 'document_terkait'],
								['label' => __('Hasil Uji Materi'), 'icon' => 'fa-gavel', 'items' => $hasilUjiMateri, 'field' => 'hasil_uji_materi'],
							] as $blok)
								<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
									<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
										<i class="fa-solid {{ $blok['icon'] }} text-primary"></i> {{ $blok['label'] }}
									</h2>

									<div class="mt-4 space-y-2">
										@forelse ($blok['items'] as $item)
											@php $berkas = $item->{$blok['field']}; @endphp
											<form action="{{ route('download_file') }}" method="post">
												@csrf
												<input type="hidden" name="filePath" value="{{ config('app.doc_directory') . $berkas }}">
												<button type="submit"
													class="flex w-full items-center gap-2 rounded border border-slate-200 px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:border-accent/40 hover:bg-accent/5 hover:text-accent">
													<i class="fa-solid fa-download shrink-0 text-accent"></i>
													<span class="truncate">{{ $berkas }}</span>
												</button>
											</form>
										@empty
											<p class="rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-6 text-center text-sm text-slate-400">
												{{ __('Data Tidak Tersedia') }}
											</p>
										@endforelse
									</div>
								</div>
							@endforeach
						</div>
					@endif

					{{-- EKSEMPLAR (monografi) --}}
					@if ($kategori == 'monografi')
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-book text-primary"></i> {{ __('EKSEMPLAR') }}
							</h2>

							<div class="mt-5 overflow-x-auto">
								<table class="w-full text-sm">
									<thead>
										<tr class="border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400">
											<th class="pb-2 pr-4 font-semibold">{{ __('Kode Eksemplar') }}</th>
											<th class="pb-2 pr-4 font-semibold">{{ __('Lokasi Rak') }}</th>
											<th class="pb-2 font-semibold">{{ __('Status Buku') }}</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-slate-100">
										@forelse ($eksemplar as $item)
											<tr class="transition hover:bg-primary/5">
												<td class="py-3 pr-4 font-medium text-slate-800">{{ $item->kode_eksemplar }}</td>
												<td class="py-3 pr-4 text-slate-600">{{ $item->lokasi_rak }}</td>
												<td class="py-3">
													<span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $item->status_eksemplar }}</span>
												</td>
											</tr>
										@empty
											<tr>
												<td colspan="3" class="py-6 text-center text-sm text-slate-400">{{ __('Data Tidak Tersedia') }}</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
					@endif

					{{-- T.E.U BADAN --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
							<i class="fa-solid fa-user-tie text-primary"></i> {{ __('T.E.U BADAN') }}
						</h2>

						<div class="mt-5 overflow-x-auto">
							<table class="w-full text-sm">
								<thead>
									<tr class="border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400">
										<th class="pb-2 pr-4 font-semibold">{{ __('Nama Pengarang') }}</th>
										<th class="pb-2 pr-4 font-semibold">{{ __('Tipe Pengarang') }}</th>
										<th class="pb-2 font-semibold">{{ __('Jenis Pengarang') }}</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-slate-100">
									@forelse ($dataPengarang as $item)
										<tr class="transition hover:bg-primary/5">
											<td class="py-3 pr-4 font-medium text-slate-800">{{ $item->nama_pengarang }}</td>
											<td class="py-3 pr-4 text-slate-600">{{ $item->tipe_pengarang }}</td>
											<td class="py-3">
												<span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $item->jenis_pengarang }}</span>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="3" class="py-6 text-center text-sm text-slate-400">{{ __('Data Tidak Tersedia') }}</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					{{-- SUBJEK --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
							<i class="fa-solid fa-tags text-primary"></i> {{ __('SUBJEK') }}
						</h2>
						<div class="mt-4 flex flex-wrap gap-2">
							@forelse ($subjek as $item)
								<span class="rounded bg-accent/10 px-3 py-1 text-xs font-medium text-accent">{{ $item->subyek }}</span>
							@empty
								<p class="text-sm text-slate-400">{{ __('Data Tidak Tersedia') }}</p>
							@endforelse
						</div>
					</div>
				</div>

				{{-- ===== SIDEBAR ===== --}}
				<div class="lg:col-span-1">
					<div class="sticky top-22 space-y-6">

						{{-- Berkas --}}
						<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-cloud-arrow-down text-primary"></i> {{ __('LAMPIRAN') }}
							</h2>

							<div class="mt-4 space-y-3">
								@if ($hasLampiran)
									<form action="{{ route('download_file') }}" method="POST">
										@csrf
										<input type="hidden" name="filePath" value="{{ $lampiranPath }}">
										<input type="hidden" name="docId" value="{{ $data['id'] }}">
										<button type="submit"
											class="flex w-full items-center justify-center gap-2 rounded bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40">
											<i class="fa-solid fa-file-lines"></i> {{ __('Download') }}
										</button>
									</form>
								@else
									<span class="flex w-full items-center justify-center gap-2 rounded bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-400 cursor-not-allowed">
										<i class="fa-solid fa-file-circle-xmark"></i> {{ __('Berkas Tidak Tersedia') }}
									</span>
								@endif

								@if ($hasAbstrak)
									<form action="{{ route('download_file') }}" method="POST">
										@csrf
										<input type="hidden" name="filePath" value="{{ $abstrakPath }}">
										<input type="hidden" name="docId" value="{{ $data['id'] }}">
										<button type="submit"
											class="flex w-full items-center justify-center gap-2 rounded bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
											<i class="fa-solid fa-file-lines"></i> {{ __('Abstrak') }}
										</button>
									</form>
								@endif

								@if ($data['judul_lampiran'])
									<p class="truncate border-t border-slate-100 pt-3 text-xs text-slate-400" title="{{ $data['judul_lampiran'] }}">
										<i class="fa-solid fa-paperclip"></i> {{ $data['judul_lampiran'] }}
									</p>
								@endif
							</div>
						</div>

						{{-- Jenis Dokumen + Status --}}
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-shield-halved text-primary"></i> {{ __('JENIS DOKUMEN') }}
							</h2>
							<dl class="mt-4 space-y-3 text-sm">
								<div class="flex items-start justify-between gap-3">
									<dt class="text-gray-500">{{ __('Jenis') }}</dt>
									<dd class="text-right font-medium text-slate-800">{{ Str::title($data['jenis_peraturan'] ?: '—') }}</dd>
								</div>

								@if ($kategori == 'peraturan')
									<div class="flex items-center justify-between gap-3">
										<dt class="text-gray-500">{{ __('STATUS') }}</dt>
										<dd>
											<span class="rounded px-2.5 py-1 text-xs font-semibold
												{{ $isBerlaku ? 'bg-accent/10 text-accent' : 'bg-slate-100 text-slate-500' }}">
												{{ $data['status'] ?: '—' }}
											</span>
										</dd>
									</div>
								@elseif ($kategori == 'putusan')
									<div class="flex items-start justify-between gap-3">
										<dt class="text-gray-500">{{ __('Lembaga Peradilan') }}</dt>
										<dd class="text-right font-medium text-slate-800">{{ $data['lembaga_peradilan'] ?: '—' }}</dd>
									</div>
								@endif
							</dl>
						</div>

						{{-- COVER (monografi) --}}
						@if ($kategori == 'monografi')
							@php
								$imageCover = checkFilePath(config('app.img_directory'), $data['gambar_sampul'])
								    ? asset('storage/' . config('app.img_directory') . $data['gambar_sampul'])
								    : asset('assets/img/default-book.png');
							@endphp
							<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
								<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
									<i class="fa-regular fa-image text-primary"></i> {{ __('COVER') }}
								</h2>
								<a href="{{ $imageCover }}" target="_blank" rel="noopener"
									class="group mt-4 block overflow-hidden rounded ring-1 ring-slate-200 transition hover:ring-primary/40">
									<img src="{{ $imageCover }}"
										alt="{{ __('Sampul') }}: {{ tt($data, 'judul') }}"
										loading="lazy"
										class="w-full transition duration-500 group-hover:scale-[1.02]">
								</a>
							</div>
						@endif

						{{-- KETERANGAN STATUS --}}
						@if ($kategori == 'peraturan')
							<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
								<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
									<i class="fa-solid fa-note-sticky text-primary"></i> {{ __('KETERANGAN STATUS') }}
								</h2>
								<p class="mt-3 text-sm leading-relaxed text-slate-600">
									{{ $dataStatus?->status_peraturan ? ucfirst($dataStatus->status_peraturan) : '—' }}
								</p>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
