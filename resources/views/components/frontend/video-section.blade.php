<section
	class="relative py-20 bg-linear-to-br from-[#1E2028] via-[#292C36] to-[#12131A] overflow-hidden">

	<!-- Decorative Glow -->
	<div class="absolute -top-32 -left-32 w-96 h-96 bg-primary/20 rounded-full blur-3xl"></div>
	<div class="absolute bottom-0 right-0 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>

	<!-- Subtle Pattern -->
	<div
		class="absolute inset-0 opacity-[0.04]"
		style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;">
	</div>

	<div class="relative max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="text-center mb-16">
			<h2 class="text-2xl lg:text-3xl font-bold text-primary">
				Berita Video Terbaru
			</h2>
			<p class="leading-6 text-sm lg:text-base mt-4 text-slate-300 max-w-3xl mx-auto">
				Menyajikan berita video dari Jaringan Dokumentasi dan Informasi Hukum
				Pemerintah Kota Kendari
			</p>
			<div class="w-16 h-1 bg-primary mx-auto mt-6"></div>
		</div>

		<!-- Grid Video -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-10">

			@foreach ($video as $v)
				<article class="bg-slate-900 rounded-xl overflow-hidden shadow-lg">
					<div class="aspect-video">
						<iframe
							class="w-full h-full"
							src="https://www.youtube.com/embed/{{ $v->link }}"
							title="Video JDIH"
							frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen>
						</iframe>
					</div>

					<div class="p-5">
						<h3 class="text-xs lg:text-sm leading-5 text-white font-semibold">
							{{ $v->judul }}
						</h3>

						<p class="mt-3 text-sm text-slate-400">
							{{ Carbon\Carbon::parse($v->tanggal)->translatedFormat('d F Y') }}
						</p>
					</div>
				</article>
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-20 text-right">
			<a wire:navigate.hover href="{{ route('frontend.pengumuman.index') }}"
				class="text-sm lg:text-base inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 lg:px-6 py-2 lg:py-3 rounded font-semibold transition">
				▶ Video Lainnya
			</a>
		</div>

	</div>
</section>
