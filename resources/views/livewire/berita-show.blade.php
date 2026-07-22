<div>
	<x-frontend.breadcrumb :title="__('Berita')" :listNav="[['label' => __('Berita'), 'route' => route('frontend.berita.index')], ['label' => __('Detail')]]" />

	@php
		$image = checkFilePath(config('app.img_directory'), $data->image)
		    ? asset('storage/' . config('app.img_directory') . $data->image)
		    : asset('assets/img/default-img.jpg');

		$judul = tt($data, 'judul');
		$tanggal = \Carbon\Carbon::parse($data->tanggal);

		// Estimasi waktu baca — 200 kata/menit
		$jumlahKata = str_word_count(strip_tags(tt($data, 'isi')));
		$menitBaca = max(1, (int) ceil($jumlahKata / 200));

		$shareUrl = url()->current();
	@endphp

	<section class="relative bg-linear-to-b from-white to-gray-50 py-10 lg:py-14">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
		</div>

		<div class="relative z-10 max-w-6xl mx-auto px-4 animate-rise">

			{{-- Tombol Kembali --}}
			<a wire:navigate.hover href="{{ route('frontend.berita.index') }}"
				class="inline-flex items-center gap-2 mb-6 rounded bg-white ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:ring-primary/40 hover:text-primary">
				<i class="fa-solid fa-arrow-left"></i>
				{{ __('Kembali ke Berita') }}
			</a>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

				{{-- ===== ARTIKEL ===== --}}
				<article class="lg:col-span-2 space-y-6">

					<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm">
						<div aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

						{{-- Gambar utama --}}
						<img src="{{ $image }}"
							alt="{{ $judul }}"
							loading="eager"
							class="aspect-video w-full object-cover object-center">

						<div class="p-6 lg:p-8">
							<span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
								<i class="fa-regular fa-newspaper"></i> {{ __('Berita') }}
							</span>

							<h1 class="mt-4 text-xl md:text-2xl lg:text-3xl font-bold text-slate-900 leading-snug">
								{{ $judul }}
							</h1>

							<div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
								<span class="inline-flex items-center gap-1.5">
									<i class="fa-regular fa-calendar text-accent"></i>
									{{ $tanggal->translatedFormat('d F Y') }}
								</span>
								<span class="inline-flex items-center gap-1.5">
									<i class="fa-regular fa-clock text-primary"></i>
									{{ $menitBaca }} {{ __('menit baca') }}
								</span>
							</div>

							{{-- Isi berita --}}
							{{-- Isi lama dari editor menyimpan inline style (font-family Montserrat, color abu tipis)
							     yang menimpa Source Sans 3 & merusak kontras — dinetralkan di sini. --}}
							<div class="prose prose-slate max-w-none mt-6
								prose-headings:font-bold prose-headings:text-slate-900
								prose-a:text-accent prose-a:font-medium hover:prose-a:text-accent-hover
								prose-img:rounded prose-strong:text-slate-900
								prose-blockquote:border-l-primary prose-blockquote:not-italic prose-blockquote:text-slate-600
								[&_*]:[font-family:inherit]!
								[&_span]:[color:inherit]!
								[&_span]:[background-color:transparent]!">
								{!! tt($data, 'isi') !!}
							</div>
						</div>
					</div>

					{{-- Bagikan --}}
					<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
						<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
							<i class="fa-solid fa-share-nodes text-primary"></i> {{ __('Bagikan Berita') }}
						</h2>
						<div class="mt-4 flex flex-wrap gap-2">
							<a href="https://wa.me/?text={{ urlencode($judul . ' — ' . $shareUrl) }}" target="_blank" rel="noopener"
								class="group inline-flex items-center gap-2 rounded ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:ring-[#25D366]/50 hover:bg-[#25D366]/5 hover:text-slate-900">
								<i class="fa-brands fa-whatsapp text-base text-[#25D366]"></i> WhatsApp
							</a>
							<a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
								class="group inline-flex items-center gap-2 rounded ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:ring-[#1877F2]/50 hover:bg-[#1877F2]/5 hover:text-slate-900">
								<i class="fa-brands fa-facebook-f text-base text-[#1877F2]"></i> Facebook
							</a>
							<a href="https://twitter.com/intent/tweet?text={{ urlencode($judul) }}&url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
								class="group inline-flex items-center gap-2 rounded ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:ring-slate-900/50 hover:bg-slate-900/5 hover:text-slate-900">
								<i class="fa-brands fa-x-twitter text-base text-slate-900"></i> X
							</a>
						</div>
					</div>
				</article>

				{{-- ===== SIDEBAR ===== --}}
				<aside class="lg:col-span-1">
					<div class="sticky top-22 space-y-6">

						{{-- Cari Berita --}}
						<div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-solid fa-magnifying-glass text-primary"></i> {{ __('Cari Berita') }}
							</h2>

							<form action="{{ route('frontend.berita.index') }}" method="GET" class="mt-4 relative">
								<i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
								<input type="text" name="q" value="{{ request('q') }}"
									placeholder="{{ __('Kata kunci berita') }}"
									class="w-full rounded border border-gray-300 py-2.5 pl-11 pr-24 text-sm transition focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/10">
								<button type="submit"
									class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded bg-primary px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-primary-hover">
									{{ __('Cari') }}
								</button>
							</form>
						</div>

						{{-- Berita Terbaru --}}
						<div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
							<h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
								<i class="fa-regular fa-newspaper text-primary"></i> {{ __('Berita Terbaru') }}
							</h2>

							<div class="mt-4 space-y-3">
								@forelse ($beritaTerbaru as $item)
									@php
										$itemImage = checkFilePath(config('app.img_directory'), $item->image)
										    ? asset('storage/' . config('app.img_directory') . $item->image)
										    : asset('assets/img/default-img.jpg');
									@endphp
									<a wire:navigate.hover href="{{ route('frontend.berita.show', Hashids::encode($item->id)) }}"
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
										<i class="fa-regular fa-newspaper text-2xl text-slate-300"></i>
										<p class="mt-2 text-sm font-medium text-slate-500">{{ __('Belum ada berita terbaru.') }}</p>
									</div>
								@endforelse
							</div>

							<a wire:navigate.hover href="{{ route('frontend.berita.index') }}"
								class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-accent transition hover:text-accent-hover">
								{{ __('Lihat semua berita') }} <i class="fa-solid fa-arrow-right text-xs"></i>
							</a>
						</div>

					</div>
				</aside>

			</div>
		</div>
	</section>
</div>
