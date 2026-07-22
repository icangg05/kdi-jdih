<section class="relative py-16 lg:py-24 bg-linear-to-b from-white via-slate-50 to-slate-100 overflow-hidden">

	<!-- ORNAMEN LATAR: glow brand + tekstur titik + watermark + garis atas -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute -top-24 left-0 h-80 w-80 rounded bg-accent/5 blur-3xl"></div>
		<div class="absolute bottom-0 right-0 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
		<div class="absolute inset-0 opacity-70" style="background-image: radial-gradient(rgba(15,23,42,0.05) 1px, transparent 1px); background-size: 24px 24px; -webkit-mask-image: radial-gradient(ellipse 80% 55% at 50% 42%, black, transparent 78%); mask-image: radial-gradient(ellipse 80% 55% at 50% 42%, black, transparent 78%);"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-slate-300 to-transparent"></div>
		<i class="fas fa-landmark-dome absolute -bottom-8 -right-4 text-[11rem] text-slate-900/3"></i>
	</div>

	<div class="relative max-w-7xl mx-auto px-4">

		<!-- HEADER SECTION -->
		<div class="animate-rise mx-auto mb-12 max-w-2xl text-center">
			<span class="inline-flex items-center gap-2 rounded border border-slate-200 bg-white px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('Pemerintah Kota Kendari') }}
			</span>
			<h2 class="mt-4 text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
				{{ __('Pimpinan Daerah') }}
			</h2>
			<div class="mx-auto mt-4 flex items-center justify-center gap-1">
				<span class="h-1 w-8 rounded bg-primary"></span>
				<span class="h-1 w-1 rounded bg-accent"></span>
			</div>
		</div>

		<!-- GRID PIMPINAN -->
		<div class="grid grid-cols-1 md:grid-cols-3 max-w-5xl mx-auto gap-6 lg:gap-8">

			@foreach ($pejabat as $i => $v)
				<article
					class="animate-rise group relative overflow-hidden rounded bg-white ring-1 ring-slate-200/70 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl hover:ring-primary/30"
					style="animation-delay: {{ $i * 0.1 }}s">

					<!-- garis aksen atas saat hover -->
					<span aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

					<!-- FOTO -->
					<div class="relative h-80 overflow-hidden bg-linear-to-b from-slate-50 to-slate-100">
						<img
							src="{{ asset($v['gambar']) }}"
							alt="{{ $v['nama'] }}"
							class="absolute inset-0 h-full w-full scale-95 object-contain object-bottom transition-transform duration-700 ease-out group-hover:scale-100" />
						<!-- scrim bawah agar foto menyatu ke info -->
						<div aria-hidden="true" class="absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-white via-white/70 to-transparent"></div>
					</div>

					<!-- INFO -->
					<div class="relative px-6 pb-6 pt-9 text-center">
						<!-- badge mengambang -->
						<span aria-hidden="true" class="absolute -top-6 left-1/2 flex h-12 w-12 -translate-x-1/2 items-center justify-center rounded bg-primary text-white shadow-lg ring-4 ring-white transition-transform duration-500 group-hover:-translate-y-1 group-hover:scale-105">
							<i class="fas fa-user-tie"></i>
						</span>
						<h3 class="text-base font-bold leading-snug text-slate-800">
							{{ $v['nama'] }}
						</h3>
						<p class="mt-1 text-sm text-slate-500">
							{{ __($v['jabatan']) }}
						</p>
						<div class="mx-auto mt-4 h-1 w-10 rounded bg-primary transition-all duration-500 group-hover:w-16"></div>
					</div>
				</article>
			@endforeach

		</div>

		<!-- NARASI & QUOTES -->
		<div class="mt-20">
			<div class="mx-auto max-w-4xl">

				<!-- Title -->
				<div class="animate-rise mb-8 text-center">
					<h3 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
						{{ __('Narasi & Quotes') }}
					</h3>
					<div class="mx-auto mt-4 flex items-center justify-center gap-1">
						<span class="h-1 w-8 rounded bg-primary"></span>
						<span class="h-1 w-1 rounded bg-accent"></span>
					</div>
				</div>

				<!-- Wrapper -->
				<div class="animate-rise relative overflow-hidden rounded bg-white ring-1 ring-slate-200/70 shadow-sm p-8 md:p-12">

					<!-- aksen atas -->
					<span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary to-accent"></span>

					<!-- ornamen tanda kutip -->
					<i class="fas fa-quote-left absolute left-5 top-6 text-5xl text-primary/10" aria-hidden="true"></i>
					<i class="fas fa-quote-right absolute bottom-5 right-5 text-5xl text-accent/10" aria-hidden="true"></i>

					<!-- CONTENT -->
					{{-- Narasi disimpan sebagai paste-an Word: tiap <span> membawa inline
					     font-family (Tahoma/Verdana/Helvetica/Arial) & font-size pt yang
					     menimpa Source Sans 3 — dinetralkan agar ikut tipografi situs. --}}
					<div class="relative prose prose-slate max-w-none text-center
						[&_*]:[font-family:inherit]!
						[&_span]:[font-size:inherit]!
						[&_span]:[background-color:transparent]!">
						{!! tt($narasi, 'text') !!}
					</div>
				</div>

			</div>
		</div>

	</div>
</section>
