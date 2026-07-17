<section class="bg-slate-50 py-20">
	<div class="max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="text-center mb-14">
			<h2 class="text-2xl md:text-4xl font-bold text-slate-800">
				Berita Terbaru
			</h2>
			<p class="mt-3 max-w-2xl mx-auto text-sm lg:text-base text-slate-600 leading-relaxed">
				Kumpulan berita terkini dari Jaringan Dokumentasi dan Informasi Hukum
				Pemerintah Kota Kendari
			</p>
		</div>

		<!-- Grid -->
		<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

			@foreach ($berita as $v)
				@php
					$image = checkFilePath(config('app.img_directory'), $v->image)
					    ? asset('storage/' . config('app.img_directory') . $v->image)
					    : asset('assets/img/default-img.jpg');
				@endphp

				<article
					class="group relative bg-white rounded-2xl overflow-hidden
          border border-slate-200
          transform-gpu will-change-transform
          transition-all duration-300 ease-out
          hover:-translate-y-1 hover:shadow-2xl">

					<!-- Image -->
					<div class="relative h-56 overflow-hidden">
						<img
							src="{{ $image }}"
							alt="Berita"
							class="absolute inset-0 w-full h-full object-cover
							group-hover:scale-105 transition-transform duration-500" />

						<!-- Overlay -->
						<div class="absolute inset-0 bg-linear-to-t from-black/50 via-black/10 to-transparent"></div>

						<!-- Date Badge -->
						<span
							class="absolute top-4 left-4 z-10
							rounded-full bg-primary/90 text-white
							text-xs font-semibold px-3 py-1 shadow">
							{{ Carbon\Carbon::parse($v->created_at)->translatedFormat('d F Y') }}
						</span>
					</div>

					<!-- Content -->
					<div class="p-6">
						<h3
							class="text-base lg:text-lg font-semibold text-slate-800
							leading-snug line-clamp-2
							group-hover:text-primary transition">
							{{ Str::limit($v->judul, 20) }}
						</h3>

						<p
							class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
							{{ Str::limit(strip_tags($v->isi), 100) }}
						</p>

						<a
							wire:navigate.hover href="{{ route('frontend.berita.show', Hashids::encode($v->id)) }}"
							class="inline-flex items-center gap-1 mt-5
							text-sm font-semibold text-primary
							group-hover:gap-2 transition-all">
							Baca Selengkapnya
							<span>→</span>
						</a>
					</div>

				</article>
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-16 text-center">
			<a wire:navigate.hover href="{{ route('frontend.berita.index') }}"
				class="text-sm lg:text-base inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 lg:px-6 py-2 lg:py-3 rounded font-semibold transition">
				Berita Lainnya
			</a>
		</div>

	</div>
</section>
