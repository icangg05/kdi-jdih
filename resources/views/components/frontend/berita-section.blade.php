<section class="relative bg-slate-50 py-16 lg:py-20 overflow-hidden">

	<!-- ORNAMEN LATAR -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute -top-24 right-0 h-72 w-72 rounded bg-primary/5 blur-3xl"></div>
		<div class="absolute bottom-0 left-0 h-72 w-72 rounded bg-accent/5 blur-3xl"></div>
		<div class="absolute inset-0 opacity-70" style="background-image: radial-gradient(rgba(15,23,42,0.05) 1px, transparent 1px); background-size: 24px 24px; -webkit-mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%); mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%);"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-slate-200 to-transparent"></div>
		<i class="fas fa-newspaper absolute -bottom-8 -left-6 text-[11rem] text-slate-900/4"></i>
	</div>

	<div class="relative max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="animate-rise text-center mb-14">
			<span class="inline-flex items-center gap-2 rounded border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('Kabar Terkini') }}
			</span>
			<h2 class="mt-4 text-2xl md:text-4xl font-bold tracking-tight text-slate-800">
				{{ __('Berita Terbaru') }}
			</h2>
			<div class="mx-auto mt-4 flex items-center justify-center gap-1">
				<span class="h-1 w-10 rounded bg-primary"></span>
				<span class="h-1 w-1.5 rounded bg-accent"></span>
			</div>
			<p class="mt-4 max-w-2xl mx-auto text-sm lg:text-base text-slate-600 leading-relaxed">
				{{ __('Kumpulan berita terkini dari Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari') }}
			</p>
		</div>

		<!-- Grid -->
		<div class="grid gap-6 lg:gap-8 sm:grid-cols-2 lg:grid-cols-3">

			@foreach ($berita as $v)
				<x-frontend.cards.berita-card :item="$v" :delay="$loop->index * 0.08" />
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-14 text-center">
			<a wire:navigate.hover href="{{ route('frontend.berita.index') }}"
				class="inline-flex items-center gap-2 rounded bg-primary px-5 lg:px-6 py-2.5 lg:py-3 text-sm lg:text-base font-semibold text-white transition hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-2">
				{{ __('Berita Lainnya') }}
				<i class="fas fa-arrow-right text-xs"></i>
			</a>
		</div>

	</div>
</section>
