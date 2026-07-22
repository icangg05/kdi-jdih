<div>
	@php
		// index informasi-hukum meng-decode hashid pada mount() — id mentah akan 404
		$jenisHash = Hashids::encode($data->jenis_informasi_hukum_id);
		$indexUrl = route('frontend.informasi-hukum.index', $jenisHash);

		$image = checkFilePath(config('app.img_directory'), $data->image)
		    ? asset('storage/' . config('app.img_directory') . $data->image)
		    : asset('assets/img/default-img.jpg');

		$judul = tt($data, 'judul');
		$hasDokumen = checkFilePath(config('app.doc_directory'), $data->dokumen);
		$dokumenUrl = $hasDokumen ? asset('storage/' . config('app.doc_directory') . $data->dokumen) : null;
	@endphp

	<x-frontend.breadcrumb
		:title="__('Informasi Hukum')"
		:listNav="[
		    ['label' => __('Informasi Hukum'), 'route' => $indexUrl],
		    ['label' => __('Detail')],
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
				{{ __('Kembali ke Informasi Hukum') }}
			</a>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

				{{-- ===== KONTEN ===== --}}
				<article class="lg:col-span-2 space-y-6">

					{{-- Header --}}
					<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

						@if ($data->jenis)
							<span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
								<i class="fa-solid fa-scale-balanced"></i> {{ $data->jenis }}
							</span>
						@endif

						<h1 class="mt-4 text-xl md:text-2xl lg:text-3xl font-bold text-slate-900 leading-snug">
							{{ $judul }}
						</h1>

						<div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
							<span class="inline-flex items-center gap-1.5">
								<i class="fa-regular fa-calendar text-accent"></i>
								{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
							</span>
							<span class="inline-flex items-center gap-1.5">
								<i class="fa-solid fa-file-pdf {{ $hasDokumen ? 'text-primary' : 'text-slate-300' }}"></i>
								{{ $hasDokumen ? __('Dokumen tersedia') : __('Tanpa dokumen') }}
							</span>
						</div>
					</div>

					{{-- Uraian --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
							<i class="fa-solid fa-align-left text-primary"></i> {{ __('Uraian') }}
						</h2>

						<div class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-6">
							<figure class="md:col-span-4">
								<a href="{{ $image }}" target="_blank" rel="noopener"
									class="group block overflow-hidden rounded ring-1 ring-slate-200 transition hover:ring-primary/40">
									<img src="{{ $image }}"
										alt="{{ __('Gambar') }}: {{ $judul }}"
										loading="lazy"
										class="w-full object-cover transition duration-500 group-hover:scale-[1.02]">
								</a>
								<figcaption class="mt-2 text-xs text-slate-400">
									<i class="fa-solid fa-expand"></i> {{ __('Klik untuk ukuran penuh') }}
								</figcaption>
							</figure>

							<div class="md:col-span-8">
								<div class="prose prose-sm prose-slate max-w-none
									prose-headings:font-bold prose-headings:text-slate-900
									prose-a:text-accent prose-a:font-medium hover:prose-a:text-accent-hover
									prose-img:rounded prose-strong:text-slate-900
									prose-blockquote:border-l-primary prose-blockquote:not-italic prose-blockquote:text-slate-600">
									{!! tt($data, 'isi') !!}
								</div>
							</div>
						</div>
					</div>

					{{-- Dokumen --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm overflow-hidden">
						<div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-6 lg:px-8">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-file-pdf text-primary"></i> {{ __('Dokumen') }}
							</h2>

							@if ($hasDokumen)
								<div class="flex flex-wrap gap-2">
									<form action="{{ route('download_file') }}" method="POST">
										@csrf
										<input type="hidden" name="filePath"
											value="{{ config('app.doc_directory') . $data->dokumen }}">
										<button type="submit"
											class="inline-flex items-center gap-2 rounded bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
											<i class="fa-solid fa-download"></i>
											{{ __('Unduh Dokumen') }}
										</button>
									</form>

									<a href="{{ $dokumenUrl }}" target="_blank" rel="noopener"
										class="inline-flex items-center gap-2 rounded ring-1 ring-accent/40 px-4 py-2.5 text-sm font-semibold text-accent transition hover:bg-accent/5">
										<i class="fa-solid fa-up-right-from-square"></i>
										{{ __('Buka di Tab Baru') }}
									</a>
								</div>
							@endif
						</div>

						@if ($hasDokumen)
							<iframe src="{{ $dokumenUrl }}"
								title="{{ __('Pratinjau dokumen') }}: {{ $judul }}"
								loading="lazy"
								class="h-125 w-full border-0 bg-slate-50 lg:h-150"></iframe>
						@else
							<div class="m-6 rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-12 text-center lg:m-8">
								<i class="fa-solid fa-file-circle-xmark text-2xl text-slate-300"></i>
								<p class="mt-2 text-sm font-medium text-slate-500">{{ __('Belum ada dokumen terlampir') }}</p>
								<p class="mt-0.5 text-xs text-slate-400">{{ __('Seluruh uraian sudah tertera di atas.') }}</p>
							</div>
						@endif
					</div>
				</article>

				{{-- ===== SIDEBAR ===== --}}
				<aside class="lg:col-span-1">
					<div class="sticky top-22 space-y-6">

						{{-- Cari --}}
						<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-magnifying-glass text-primary"></i> {{ __('Cari Informasi Hukum') }}
							</h2>

							<form action="{{ $indexUrl }}" method="GET" class="mt-4 relative">
								<i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
								<input type="text" name="q" value="{{ request('q') }}"
									placeholder="{{ __('Kata kunci') }}"
									class="w-full rounded border border-gray-300 py-2.5 pl-11 pr-24 text-sm transition focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/10">
								<button type="submit"
									class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded bg-primary px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-primary-hover">
									{{ __('Cari') }}
								</button>
							</form>
						</div>

						{{-- Terbaru --}}
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-scale-balanced text-primary"></i> {{ __('Informasi Hukum Terbaru') }}
							</h2>

							<div class="mt-4 space-y-3">
								@forelse ($informasiHukumTerbaru as $item)
									@php
										$itemImage = checkFilePath(config('app.img_directory'), $item->image)
										    ? asset('storage/' . config('app.img_directory') . $item->image)
										    : asset('assets/img/default-img.jpg');
									@endphp
									<a wire:navigate.hover href="{{ route('frontend.informasi-hukum.show', Hashids::encode($item->id)) }}"
										class="group flex gap-3 rounded border border-slate-200 p-3 transition hover:border-primary/40 hover:bg-primary/5">
										<img src="{{ $itemImage }}"
											alt="{{ tt($item, 'judul') }}"
											loading="lazy"
											class="h-16 w-20 shrink-0 rounded object-cover object-center">
										<div class="min-w-0">
											<h3 class="text-sm font-semibold text-slate-800 line-clamp-2 transition group-hover:text-primary">
												{{ tt($item, 'judul') }}
											</h3>
											<span class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-400">
												<i class="fa-regular fa-calendar"></i>
												{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
											</span>
										</div>
									</a>
								@empty
									<div class="rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center">
										<i class="fa-solid fa-scale-balanced text-2xl text-slate-300"></i>
										<p class="mt-2 text-sm font-medium text-slate-500">{{ __('Belum ada data terbaru.') }}</p>
									</div>
								@endforelse
							</div>

							<a wire:navigate.hover href="{{ $indexUrl }}"
								class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-accent transition hover:text-accent-hover">
								{{ __('Lihat semua') }} <i class="fa-solid fa-arrow-right text-xs"></i>
							</a>
						</div>

					</div>
				</aside>

			</div>
		</div>
	</section>
</div>
