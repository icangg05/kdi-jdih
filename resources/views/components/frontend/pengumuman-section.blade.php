<section class="bg-slate-50 py-20">
	<div class="max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="mb-14">
			<h2 class="text-2xl lg:text-3xl font-bold text-slate-900">
				Pengumuman Terbaru
			</h2>
			<p class="mt-3 max-w-3xl text-sm lg:text-base text-slate-600 leading-relaxed">
				Menyajikan pengumuman terbaru dari Jaringan Dokumentasi dan Informasi
				Hukum Pemerintah Kota Kendari
			</p>
		</div>

		<!-- List -->
		<div class="space-y-8">

			@foreach ($pengumuman as $v)
				@php
					$image = checkFilePath(config('app.img_directory'), $v->image)
					    ? asset('storage/' . config('app.img_directory') . $v->image)
					    : asset('assets/img/default-img.jpg');
				@endphp

				<article
					class="group relative bg-white rounded-2xl border border-slate-200 overflow-hidden
					transition duration-300 hover:-translate-y-1 hover:shadow-xl">

					<div class="flex flex-col md:flex-row">

						<!-- Image -->
						<div class="relative md:w-72 shrink-0">
							<img
								src="{{ $image }}"
								alt="Pengumuman JDIH"
								class="h-48 md:h-full w-full object-cover" />

							<!-- Gradient Overlay -->
							<div
								class="absolute inset-0 bg-linear-to-t from-black/40 via-black/10 to-transparent md:hidden">
							</div>
						</div>

						<!-- Content -->
						<div class="flex flex-col justify-between p-6 lg:p-8 flex-1">

							<div>
								<!-- Badge -->
								<span
									class="inline-flex items-center gap-1 mb-4 rounded-full
									bg-orange-50 text-primary text-xs font-semibold px-3 py-1">
									📌 Pemberitahuan Putusan
								</span>

								<h3
									class="text-base lg:text-lg font-semibold text-slate-900 leading-snug
									group-hover:text-primary transition">
									{{ Str::limit($v->judul, 60) }}
								</h3>

								<p
									class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-2">
									{{ Str::limit(strip_tags($v->isi), 130) }}
								</p>
							</div>

							<div class="mt-6 flex gap-3 items-center justify-between">
								<span class="text-xs lg:text-sm text-slate-500 flex items-center gap-1">
									📅 {{ Carbon\Carbon::parse($v->tanggal)->translatedFormat('d F Y') }}
								</span>

								<a wire:navigate.hover href="{{ route('frontend.pengumuman.show', Hashids::encode($v->id)) }}"
									class="inline-flex items-center gap-1 text-xs lg:text-sm font-semibold text-primary
									group-hover:gap-2 transition-all">
									Baca Selengkapnya
									<span>→</span>
								</a>
							</div>

						</div>
					</div>
				</article>
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-16 text-center">
			<a wire:navigate.hover href="{{ route('frontend.pengumuman.index') }}"
				class="text-sm lg:text-base inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 lg:px-6 py-2 lg:py-3 rounded font-semibold transition">
				Pengumuman Lainnya
			</a>
		</div>

	</div>
</section>
