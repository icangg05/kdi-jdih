@php
	$indexUrl = route('frontend.dokumen.index', $kategori);
	$kategoriLabel = Str::words($title, 1, '');

	$lampiranPath = config('app.doc_directory') . $data['dokumen_lampiran'];
	$abstrakPath = config('app.doc_directory') . $data['abstrak'];
	$hasLampiran = checkFilePath(config('app.doc_directory'), $data['dokumen_lampiran']);
	$hasAbstrak = checkFilePath(config('app.doc_directory'), $data['abstrak']);

	$isBerlaku = strtolower((string) $data['status']) === 'berlaku';

	// Hashid dipakai untuk id elemen QR sekaligus target URL yang di-encode
	$hashId = Hashids::encode($data['id']);
	$shareUrl = route('frontend.dokumen.show', [$kategori, $hashId]);

	// Preview inline hanya untuk PDF; nama berkas bisa mengandung spasi jadi tiap segmen di-encode
	$isPdf = $hasLampiran && Str::endsWith(Str::lower($data['dokumen_lampiran']), '.pdf');
	$previewUrl = $isPdf ? asset('storage/' . implode('/', array_map('rawurlencode', explode('/', $lampiranPath)))) : null;

	$shareLinks = [
		['label' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'url' => 'https://wa.me/?text=' . rawurlencode(tt($data, 'judul') . ' - ' . $shareUrl)],
		['label' => 'Facebook', 'icon' => 'fa-brands fa-facebook-f', 'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl)],
		['label' => 'X', 'icon' => 'fa-brands fa-x-twitter', 'url' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($shareUrl) . '&text=' . rawurlencode(tt($data, 'judul'))],
		['label' => 'Telegram', 'icon' => 'fa-brands fa-telegram', 'url' => 'https://t.me/share/url?url=' . rawurlencode($shareUrl) . '&text=' . rawurlencode(tt($data, 'judul'))],
	];

	// ===== Lembar kerja: metadata key-value satu kartu, mengikuti format JDIH Nasional =====
	$isi = fn ($v) => filled($v) ? $v : '-';
	$tgl = fn ($v) => $v ? \Carbon\Carbon::parse($v)->translatedFormat('j F Y') : null;

	$tanggalPeraturan = collect([
		$tgl($data['tanggal_penetapan']) ? __('Penetapan') . ' ' . $tgl($data['tanggal_penetapan']) : null,
		$tgl($data['tanggal_pengundangan']) ? __('Pengundangan') . ' ' . $tgl($data['tanggal_pengundangan']) : null,
	])->filter()->implode(' · ') ?: '-';

	$berkasAda = collect([$hasLampiran ? __('Fulltext') : null, $hasAbstrak ? __('Abstrak') : null])->filter();
	$lampiranTeks = $berkasAda->isNotEmpty()
	    ? $berkasAda->implode(', ') . ' ' . __('dalam bentuk .pdf')
	    : __('Berkas Tidak Tersedia');

	$lokasi = __('Bagian Hukum Setda Kota Kendari');

	// Nilai '@subjek' dan '@status' dirender khusus di tabel, bukan teks biasa
	$rows = [
		[__('Tipe Dokumen'), [
		    'peraturan' => __('Peraturan Perundang-undangan'),
		    'monografi' => __('Monografi Hukum'),
		    'artikel' => __('Artikel / Majalah Hukum'),
		    'putusan' => __('Putusan Pengadilan'),
		][$kategori]],
		[__('Judul'), tt($data, 'judul')],
		[__('T.E.U. Badan/Pengarang'), '@pengarang'],
	];

	if ($kategori == 'peraturan') {
	    $rows = array_merge($rows, [
	        [__('Nomor Peraturan'), $isi($data['nomor_peraturan'])],
	        [__('Jenis / Bentuk Peraturan'), $isi($data['jenis_peraturan'])],
	        [__('Singkatan Jenis/Bentuk Peraturan'), $isi($data['singkatan_master'] ?: $data['singkatan_jenis'])],
	        [__('Tempat Penetapan'), $isi($data['tempat_terbit'])],
	        [__('Tanggal-Bulan-Tahun Penetapan/Pengundangan'), $tanggalPeraturan],
	        [__('Sumber'), $isi($data['sumber'])],
	        [__('Subjek'), '@subjek'],
	        [__('Status Peraturan'), '@status'],
	        [__('Bahasa'), $isi($data['bahasa'])],
	        [__('Lokasi'), $lokasi],
	        [__('Bidang Hukum'), $isi($data['bidang_hukum'])],
	        [__('Urusan Pemerintahan'), $isi($data['urusan_pemerintahan'])],
	        [__('Penandatanganan'), $isi($data['penandatanganan'])],
	        [__('Pemrakarsa'), $isi($data['pemrakarsa'])],
	        [__('Lampiran'), $lampiranTeks],
	        [__('Peraturan Terkait'), '@perter'],
	        [__('Hasil Uji Materi'), '@uji-materi'],
	        [__('Peraturan Pelaksana'), '@pelaksana'],
	    ]);
	} elseif ($kategori == 'monografi') {
	    $rows = array_merge($rows, [
	        [__('Nomor Panggil'), $isi($data['nomor_panggil'])],
	        [__('Penerbit'), $isi($data['penerbit'])],
	        [__('Tempat Terbit'), $isi($data['tempat_terbit'])],
	        [__('Tahun Terbit'), $isi($data['tahun_terbit'])],
	        [__('Deskripsi Fisik'), $isi($data['deskripsi_fisik'])],
	        [__('ISBN'), $isi($data['isbn'])],
	        [__('Klasifikasi'), $isi($data['klasifikasi'])],
	        [__('Anotasi'), $isi($data['sumber'])],
	        [__('Subjek'), '@subjek'],
	        [__('Bahasa'), $isi($data['bahasa'])],
	        [__('Lokasi'), $lokasi],
	        [__('Bidang Hukum'), $isi($data['bidang_hukum'])],
	        [__('Lampiran'), $lampiranTeks],
	    ]);
	} elseif ($kategori == 'artikel') {
	    $rows = array_merge($rows, [
	        [__('Sumber'), $isi($data['sumber'])],
	        [__('Tahun Terbit'), $isi($data['tahun_terbit'])],
	        [__('Subjek'), '@subjek'],
	        [__('Bahasa'), $isi($data['bahasa'])],
	        [__('Lokasi'), $lokasi],
	        [__('Bidang Hukum'), $isi($data['bidang_hukum'])],
	        [__('Lampiran'), $lampiranTeks],
	    ]);
	} elseif ($kategori == 'putusan') {
	    // Amar Putusan sengaja tidak masuk grid — isinya panjang, dirender di kartu sendiri
	    $rows = array_merge($rows, [
	        [__('Klasifikasi'), $isi($data['klasifikasi'])],
	        [__('Tingkat Proses'), $isi($data['sub_klasifikasi'])],
	        [__('Tanggal Dibacakan'), $tgl($data['tanggal_penetapan']) ?: '-'],
	        [__('Penggugat / Pemohon'), $isi($data['pemohon'])],
	        [__('Tergugat / Termohon'), $isi($data['termohon'])],
	        [__('Tempat Pengadilan'), $isi($data['lembaga_peradilan'])],
	        [__('Subjek'), '@subjek'],
	        [__('Bahasa'), $isi($data['bahasa'])],
	        [__('Lokasi'), $lokasi],
	        [__('Lampiran'), $lampiranTeks],
	    ]);
	}
@endphp

<div>
	<x-frontend.breadcrumb
		:title="$kategoriLabel . ' ' . __('Detail')"
		:listNav="[
		    ['label' => $kategoriLabel, 'route' => $indexUrl],
		    ['label' => Str::title($data['jenis_peraturan'] ?: __('Detail'))],
		]" />

	<section class="relative bg-linear-to-b from-white to-gray-50 py-6 lg:py-10">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
		</div>

		<div class="relative z-10 max-w-6xl mx-auto px-4 animate-rise">

			{{-- Tombol Kembali --}}
			<a wire:navigate.hover href="{{ $indexUrl }}"
				class="inline-flex items-center gap-2 mb-4 rounded bg-white ring-1 ring-slate-200 px-3.5 py-1.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:ring-primary/40 hover:text-primary">
				<i class="fa-solid fa-arrow-left"></i>
				{{ __('Kembali ke') }} {{ $kategoriLabel }}
			</a>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6">

				{{-- ===== MAIN ===== --}}
				<div class="lg:col-span-2 space-y-4 lg:space-y-5">

					{{-- Header --}}
					<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-4 lg:p-6">
						<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

						<div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 text-xs text-slate-500">
							<div class="flex flex-wrap items-center gap-4">
								<span class="inline-flex items-center gap-1.5">
									<i class="fa-regular fa-eye text-primary/70"></i>
									<span class="font-semibold text-slate-700">{{ number_format($data['hit_see'] ?? 0, 0, ',', '.') }}</span> {{ __('dilihat') }}
								</span>
								<span class="inline-flex items-center gap-1.5">
									<i class="fa-solid fa-cloud-arrow-down text-accent/70"></i>
									<span class="font-semibold text-slate-700">{{ number_format($data['hit_download'] ?? 0, 0, ',', '.') }}</span> {{ __('diunduh') }}
								</span>
							</div>

							<div class="flex items-center gap-2">
								<span class="font-medium text-slate-400">{{ __('Bagikan') }}</span>
								@foreach ($shareLinks as $share)
									<a href="{{ $share['url'] }}" target="_blank" rel="noopener noreferrer"
										title="{{ __('Bagikan ke') }} {{ $share['label'] }}"
										class="inline-flex h-8 w-8 items-center justify-center rounded ring-1 ring-slate-200 text-slate-500 transition hover:ring-accent/40 hover:text-accent hover:bg-accent/5">
										<i class="{{ $share['icon'] }}"></i>
										<span class="sr-only">{{ $share['label'] }}</span>
									</a>
								@endforeach
								<button type="button" onclick="salinTautanDokumen(this)"
									data-url="{{ $shareUrl }}" data-label-copied="{{ __('Tautan disalin') }}"
									title="{{ __('Salin tautan') }}"
									class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded ring-1 ring-slate-200 text-slate-500 transition hover:ring-primary/40 hover:text-primary hover:bg-primary/5">
									<i class="fa-solid fa-link"></i>
									<span class="sr-only">{{ __('Salin tautan') }}</span>
								</button>
							</div>
						</div>
					</div>

					{{-- Detail Dokumen — Bagian Hukum minta semua sub-bagian jadi satu kartu, bukan kartu terpisah --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-4 lg:p-6">
						{{-- Metadata dirender sebagai tabel key-value dua kolom, mengikuti
						     lembar kerja JDIH Nasional: label di kiri, isian di kanan. --}}
						<div class="overflow-hidden rounded ring-1 ring-accent/20">
							<table class="w-full table-fixed border-collapse text-[13px] sm:text-sm">
								<caption class="bg-accent/10 px-3.5 py-2 text-left sm:px-4 sm:py-2.5">
									<span class="flex items-center gap-2 text-sm font-semibold text-accent">
										<i class="fa-solid fa-circle-info"></i> {{ __('Lembar Kerja') }} {{ __($kategoriLabel) }}
									</span>
								</caption>
								<tbody>
									@foreach ($rows as [$label, $value])
										<tr class="border-t border-accent/20 align-top">
											<th scope="row" class="w-[38%] break-words border-r border-accent/20 bg-accent/5 px-3 py-2 text-left font-medium text-slate-600 sm:w-[34%] sm:px-3.5">
												{{ $label }}
											</th>
											<td class="px-3 py-2 text-slate-800 break-words sm:px-3.5">
												@if ($value === '@status')
													<span class="inline-flex items-center gap-1.5 rounded px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide ring-1 ring-inset
														{{ $isBerlaku ? 'bg-accent/10 text-accent ring-accent/25' : 'bg-primary/10 text-primary ring-primary/25' }}">
														<i class="fa-solid {{ $isBerlaku ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
														{{ $data['status'] ?: '-' }}
													</span>
													@foreach ($dataStatus as $st)
														<span class="mt-1.5 block text-slate-600">
															<span class="font-medium">{{ Str::ucfirst($st->status_peraturan) }}</span>
															@if ($st->judul_target)
																<a wire:navigate.hover
																	href="{{ route('frontend.dokumen.show', [checkPrefixRoute($st->tipe_target), Hashids::encode((int) $st->id_dokumen_target)]) }}"
																	class="text-accent underline-offset-2 hover:underline">{{ $st->judul_target }}</a>
															@endif
															@if (filled($st->catatan_status_peraturan) && trim($st->catatan_status_peraturan) !== '-')
																<span class="text-slate-500">({{ $st->catatan_status_peraturan }})</span>
															@endif
														</span>
													@endforeach
												@elseif ($value === '@pengarang')
													@forelse ($dataPengarang as $item)
														{{-- Jenis pengarang hanya ditulis kalau bukan pengarang utama,
														     supaya barisnya tidak berisik untuk data yang seragam. --}}
														<span class="block">
															{{ $item->nama_pengarang }}
															@if (Str::lower($item->jenis_pengarang) !== 'pengarang utama')
																<span class="text-slate-500">({{ $item->jenis_pengarang }})</span>
															@endif
														</span>
													@empty
														{{ $isi($data['teu']) }}
													@endforelse
												@elseif ($value === '@perter')
													@forelse ($peraturanTerkait as $item)
														<span class="mt-1 block first:mt-0">
															<span class="font-medium">{{ Str::ucfirst($item->status_perter) }}</span>
															<a wire:navigate.hover
																href="{{ route('frontend.dokumen.show', ['peraturan', Hashids::encode((int) $item->peraturan_terkait)]) }}"
																class="text-accent underline-offset-2 hover:underline">{{ $item->judul_peraturan_terkait }}</a>
														</span>
													@empty
														-
													@endforelse
												@elseif ($value === '@uji-materi')
													@forelse ($hasilUjiMateri as $item)
														<form action="{{ route('download_file') }}" method="post" class="mt-1 first:mt-0">
															@csrf
															<input type="hidden" name="filePath" value="{{ config('app.doc_directory') . $item->hasil_uji_materi }}">
															<button type="submit" class="inline-flex cursor-pointer items-start gap-1.5 text-left text-accent underline-offset-2 hover:underline">
																<i class="fa-solid fa-download mt-0.5 shrink-0"></i>
																<span>{{ $item->hasil_uji_materi }}</span>
															</button>
														</form>
													@empty
														-
													@endforelse
												@elseif ($value === '@pelaksana')
													@forelse ($peraturanPelaksana as $item)
														<span class="mt-1 block first:mt-0">
															@if (filled($item->peraturan_pelaksana))
																<a wire:navigate.hover
																	href="{{ route('frontend.dokumen.show', ['peraturan', Hashids::encode((int) $item->peraturan_pelaksana)]) }}"
																	class="text-accent underline-offset-2 hover:underline">{{ $item->judul_peraturan_pelaksana }}</a>
															@elseif (checkFilePath(config('app.doc_directory'), $item->file_pelaksana))
																{{-- Dokumen unggahan: tanpa docId supaya tidak menambah hitungan unduhan dokumen induk --}}
																<form action="{{ route('download_file') }}" method="post" class="inline">
																	@csrf
																	<input type="hidden" name="filePath" value="{{ config('app.doc_directory') . $item->file_pelaksana }}">
																	<button type="submit" class="inline-flex cursor-pointer items-start gap-1.5 text-left text-accent underline-offset-2 hover:underline">
																		<i class="fa-solid fa-download mt-0.5 shrink-0"></i>
																		<span>{{ $item->judul_pelaksana }}</span>
																	</button>
																</form>
															@else
																{{ $item->judul_pelaksana }}
															@endif
															@if (filled($item->catatan_pelaksana) && trim($item->catatan_pelaksana) !== '-')
																<span class="text-slate-500">({{ $item->catatan_pelaksana }})</span>
															@endif
														</span>
													@empty
														-
													@endforelse
												@elseif ($value === '@subjek')
													<span class="flex flex-wrap gap-1.5">
														@forelse ($subjek as $item)
															<span class="rounded bg-accent/10 px-2 py-0.5 text-[11px] font-medium text-accent sm:text-xs">{{ $item->subyek }}</span>
														@empty
															-
														@endforelse
													</span>
												@else
													{{ $value }}
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						{{-- AMAR PUTUSAN --}}
						@if ($kategori == 'putusan')
							<section class="mt-5 border-t border-slate-100 pt-4 lg:mt-6 lg:pt-5">
								<h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
									<i class="fa-solid fa-gavel text-primary"></i> {{ __('Amar Putusan') }}
								</h3>

								@if ($data['amar_status'])
									<div class="mt-3 rounded border-l-2 border-primary bg-slate-50/60 p-4">
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
									<p class="mt-4 rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-5 text-center text-sm text-slate-400">
										{{ __('Data Tidak Tersedia') }}
									</p>
								@endif
							</section>
						@endif

						{{-- EKSEMPLAR (monografi) --}}
						@if ($kategori == 'monografi')
							<section class="mt-5 border-t border-slate-100 pt-4 lg:mt-6 lg:pt-5">
								<h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
									<i class="fa-solid fa-book text-primary"></i> {{ __('EKSEMPLAR') }}
								</h3>

								<div class="mt-4 overflow-x-auto">
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
							</section>
						@endif

					</div>

				</div>

				{{-- ===== SIDEBAR ===== --}}
				<div class="lg:col-span-1">
					<div class="space-y-4 lg:space-y-5">

						{{-- Berkas --}}
						<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-4 lg:p-5">
							<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
							<h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
								<i class="fa-solid fa-cloud-arrow-down text-primary"></i> {{ __('LAMPIRAN') }}
							</h2>

							<div class="mt-4 space-y-3">
								@if ($hasLampiran)
									<form action="{{ route('download_file') }}" method="POST">
										@csrf
										<input type="hidden" name="filePath" value="{{ $lampiranPath }}">
										<input type="hidden" name="docId" value="{{ $data['id'] }}">
										<button type="submit"
											class="flex w-full items-center justify-center gap-2 rounded bg-accent px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40">
											<i class="fa-solid fa-file-lines"></i> {{ __('Download') }}
										</button>
									</form>
								@else
									<span class="flex w-full items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-400 cursor-not-allowed">
										<i class="fa-solid fa-file-circle-xmark"></i> {{ __('Berkas Tidak Tersedia') }}
									</span>
								@endif

								@if ($hasAbstrak)
									<form action="{{ route('download_file') }}" method="POST">
										@csrf
										<input type="hidden" name="filePath" value="{{ $abstrakPath }}">
										<input type="hidden" name="docId" value="{{ $data['id'] }}">
										<button type="submit"
											class="flex w-full items-center justify-center gap-2 rounded bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
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

						{{-- QR CODE --}}
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-4 lg:p-5">
							<h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
								<i class="fa-solid fa-qrcode text-primary"></i> {{ __('QR CODE') }}
							</h2>
							<p class="mt-2 text-xs leading-relaxed text-slate-500">
								{{ __('Pindai untuk membuka dokumen ini di perangkat lain.') }}
							</p>

							<div class="mt-4 flex flex-col items-center">
								<div id="qrcode-{{ $hashId }}" class="w-32 rounded bg-white p-2 ring-1 ring-slate-200">
									{!! QrCode::format('svg')->size(120)->generate($shareUrl) !!}
								</div>

								<button type="button" onclick="downloadPNG('{{ $hashId }}')"
									class="mt-3 inline-flex cursor-pointer items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-primary">
									<i class="fa-solid fa-cloud-arrow-down"></i>
									{{ __('Unduh QR') }}
								</button>
							</div>
						</div>

						{{-- PREVIEW DOKUMEN — kartunya sendiri berbanding A4 (210:297) supaya
						     terlihat seperti satu lembar kertas; iframe mengisi sisa tinggi kartu. --}}
						<div class="flex aspect-[210/297] flex-col overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-4">
							<h2 class="flex shrink-0 items-center gap-2 text-sm font-semibold text-slate-900">
								<i class="fa-regular fa-file-pdf text-primary"></i> {{ __('Preview Dokumen') }}
							</h2>

							@if ($previewUrl)
								<div class="mt-3 min-h-0 flex-1 overflow-hidden rounded ring-1 ring-slate-200 bg-slate-100">
									<iframe src="{{ $previewUrl }}#view=Fit"
										title="{{ __('Preview') }}: {{ tt($data, 'judul') }}"
										loading="lazy"
										class="h-full w-full"></iframe>
								</div>
								<a href="{{ $previewUrl }}" target="_blank" rel="noopener"
									class="mt-2.5 inline-flex shrink-0 items-center gap-2 text-xs font-semibold text-accent transition hover:text-accent-hover">
									<i class="fa-solid fa-up-right-from-square"></i>
									{{ __('Buka di tab baru') }}
								</a>
							@elseif ($hasLampiran)
								<p class="mt-3 flex flex-1 items-center justify-center rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 text-center text-sm text-slate-400">
									{{ __('Berkas bukan PDF sehingga tidak bisa ditampilkan. Silakan unduh berkasnya.') }}
								</p>
							@else
								<p class="mt-3 flex flex-1 items-center justify-center rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 text-center text-sm text-slate-400">
									{{ __('Berkas Tidak Tersedia') }}
								</p>
							@endif
						</div>

						{{-- COVER (monografi) --}}
						@if ($kategori == 'monografi')
							@php
								$imageCover = checkFilePath(config('app.img_directory'), $data['gambar_sampul'])
								    ? asset('storage/' . config('app.img_directory') . $data['gambar_sampul'])
								    : asset('assets/img/default-book.png');
							@endphp
							<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-4 lg:p-5">
								<h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
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

					</div>
				</div>
			</div>
		</div>
	</section>

	<script>
		function downloadPNG(id) {
			const svg = document.querySelector('#qrcode-' + id + ' svg');
			if (!svg) return;

			const svgBlob = new Blob([new XMLSerializer().serializeToString(svg)], {
				type: "image/svg+xml;charset=utf-8"
			});
			const url = URL.createObjectURL(svgBlob);
			const img = new Image();

			img.onload = function() {
				const scale = 5;
				const canvas = document.createElement("canvas");
				canvas.width = img.width * scale;
				canvas.height = img.height * scale;

				const ctx = canvas.getContext("2d");
				ctx.fillStyle = "#ffffff";
				ctx.fillRect(0, 0, canvas.width, canvas.height);
				ctx.scale(scale, scale);
				ctx.drawImage(img, 0, 0);
				URL.revokeObjectURL(url);

				const a = document.createElement("a");
				a.href = canvas.toDataURL("image/png");
				a.download = "qrcode-{{ $kategori }}-" + id + ".png";
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			};

			img.src = url;
		}

		function salinTautanDokumen(btn) {
			navigator.clipboard.writeText(btn.dataset.url).then(() => {
				const icon = btn.querySelector('i');
				btn.title = btn.dataset.labelCopied;
				icon.className = 'fa-solid fa-check';
				setTimeout(() => {
					icon.className = 'fa-solid fa-link';
				}, 1800);
			});
		}
	</script>
</div>
