<section class="py-14 lg:py-24 bg-linear-to-b from-white via-slate-50 to-slate-100">
	<div class="max-w-7xl mx-auto px-4">

		<!-- GRID -->
		<div class="grid grid-cols-1 md:grid-cols-3 max-w-5xl mx-auto gap-8">

			@foreach ($pejabat as $i => $v)
				<div
					data-aos="fade-up"
					data-aos-delay="{{ $i * 120 }}"
					data-aos-duration="800"
					class="group relative rounded-2xl overflow-hidden
					bg-white/70 backdrop-blur-xl
					border border-white/40
					shadow-lg
					transition-all duration-500">

					<!-- FOTO -->
					<div class="relative h-90 bg-linear-to-t from-slate-100/80 to-white/60">
						<img
							src="{{ asset($v['gambar']) }}"
							alt="{{ $v['nama'] }}"
							class="absolute inset-0 w-full h-full object-contain object-bottom
							scale-90 group-hover:scale-100
							transition-all duration-700 ease-out" />
					</div>

					<!-- INFO -->
					<div class="relative p-6 text-center">
						<h3 class="font-bold text-base uppercase text-slate-800 leading-snug">
							{{ $v['nama'] }}
						</h3>
						<p class="mt-1 text-sm text-slate-500">
							{{ $v['jabatan'] }}
						</p>

						<div class="w-10 h-1 bg-primary mx-auto mt-4 rounded-full"></div>
					</div>
				</div>
			@endforeach

		</div>

		<!-- Narasi & Quotes -->
		<section class="mt-20">
			<div class="max-w-4xl mx-auto">

				<!-- Title -->
				<div
					class="text-center mb-8">
					<h3 class="text-2xl md:text-3xl font-bold text-slate-800">
						Narasi & Quotes
					</h3>
					<div class="w-16 h-1 bg-primary mx-auto mt-4 rounded-full"></div>
				</div>

				<!-- Wrapper -->
				<div
					class="relative bg-white/70 backdrop-blur-xl
					border border-white/50
					rounded-2xl
					shadow-lg
					p-6 md:p-10">

					<!-- subtle glossy -->
					<div class="absolute inset-0 pointer-events-none">
						<div
							class="absolute -top-1/3 -left-1/3 w-full h-full
							bg-linear-to-br from-white/40 via-transparent to-transparent rotate-12">
						</div>
					</div>

					<!-- CONTENT (TIDAK DIUBAH) -->
					<div class="relative prose prose-slate max-w-none text-center">
						{!! $narasi->text !!}
					</div>
				</div>

			</div>
		</section>

	</div>
</section>
