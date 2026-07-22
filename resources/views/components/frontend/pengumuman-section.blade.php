<section class="relative bg-slate-50 py-16 lg:py-20 overflow-hidden">

	<!-- ORNAMEN LATAR -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute -top-24 left-0 h-72 w-72 rounded bg-accent/5 blur-3xl"></div>
		<div class="absolute bottom-0 right-0 h-72 w-72 rounded bg-primary/5 blur-3xl"></div>
		<div class="absolute inset-0 opacity-70" style="background-image: radial-gradient(rgba(15,23,42,0.05) 1px, transparent 1px); background-size: 24px 24px; -webkit-mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%); mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%);"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-slate-200 to-transparent"></div>
		<i class="fas fa-bullhorn absolute -bottom-8 -right-6 text-[11rem] text-slate-900/4 -rotate-12"></i>
	</div>

	<div class="relative max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="animate-rise mb-12">
			<span class="inline-flex items-center gap-2 rounded border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('Informasi') }}
			</span>
			<h2 class="mt-3 text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">
				{{ __('Pengumuman Terbaru') }}
			</h2>
			<div class="mt-3 flex items-center gap-1">
				<span class="h-1 w-8 rounded bg-primary"></span>
				<span class="h-1 w-1 rounded bg-accent"></span>
			</div>
			<p class="mt-4 max-w-3xl text-sm lg:text-base text-slate-600 leading-relaxed">
				{{ __('Menyajikan pengumuman terbaru dari Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari') }}
			</p>
		</div>

		<!-- List -->
		<div class="space-y-6 lg:space-y-8">

			@foreach ($pengumuman as $v)
				<x-frontend.cards.pengumuman-card :item="$v" :index="$loop->iteration" :delay="$loop->index * 0.08" />
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-14 text-center">
			<a wire:navigate.hover href="{{ route('frontend.pengumuman.index') }}"
				class="inline-flex items-center gap-2 rounded bg-primary px-5 lg:px-6 py-2.5 lg:py-3 text-sm lg:text-base font-semibold text-white transition hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-2">
				{{ __('Pengumuman Lainnya') }}
				<i class="fas fa-arrow-right text-xs"></i>
			</a>
		</div>

	</div>
</section>
