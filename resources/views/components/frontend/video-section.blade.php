<section
	x-data="{ open: false, vid: '', vtitle: '' }"
	class="relative py-16 lg:py-24 bg-linear-to-br from-[#1E2028] via-[#292C36] to-[#12131A] overflow-hidden">

	<!-- ORNAMEN: glow brand + pola titik + garis atas -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute -top-32 -left-32 h-96 w-96 rounded bg-primary/20 blur-3xl"></div>
		<div class="absolute bottom-0 right-0 h-96 w-96 rounded bg-accent/10 blur-3xl"></div>
		<div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/15 to-transparent"></div>
	</div>

	<div class="relative max-w-6xl mx-auto px-6">

		<!-- Header -->
		<div class="animate-rise text-center mb-14">
			<span class="inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-300 backdrop-blur-sm">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('Galeri Video') }}
			</span>
			<h2 class="mt-4 text-2xl lg:text-3xl font-bold tracking-tight text-white">
				{{ __('Berita Video Terbaru') }}
			</h2>
			<p class="mt-4 max-w-3xl mx-auto text-sm lg:text-base leading-6 text-slate-300">
				{{ __('Menyajikan berita video dari Jaringan Dokumentasi dan Informasi Hukum Pemerintah Kota Kendari') }}
			</p>
			<div class="mx-auto mt-6 flex items-center justify-center gap-1">
				<span class="h-1 w-10 rounded bg-primary"></span>
				<span class="h-1 w-1.5 rounded bg-accent"></span>
			</div>
		</div>

		<!-- Grid Video -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

			@foreach ($video as $v)
				<x-frontend.cards.video-card :item="$v" :delay="$loop->index * 0.08" />
			@endforeach

		</div>

		<!-- CTA -->
		<div class="mt-14 text-center md:text-right">
			<a wire:navigate.hover href="{{ route('frontend.video.index') }}"
				class="inline-flex items-center gap-2 rounded bg-primary px-5 lg:px-6 py-2.5 lg:py-3 text-sm lg:text-base font-semibold text-white transition hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[#12131A]">
				<i class="fas fa-play text-xs"></i>
				{{ __('Video Lainnya') }}
			</a>
		</div>

	</div>

	<!-- MODAL VIDEO -->
	<div
		x-show="open"
		x-cloak
		x-transition.opacity
		@keydown.escape.window="open = false"
		class="fixed inset-0 z-100 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
		@click.self="open = false">

		<div class="relative w-full max-w-4xl" @click.stop>
			<button
				type="button"
				@click="open = false"
				aria-label="{{ __('Tutup') }}"
				class="absolute -top-11 right-0 flex h-9 w-9 items-center justify-center rounded bg-white/10 text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
				<i class="fas fa-xmark text-lg"></i>
			</button>

			<div class="relative aspect-video overflow-hidden rounded bg-black ring-1 ring-white/10 shadow-2xl">
				<template x-if="open">
					<iframe
						class="absolute inset-0 h-full w-full"
						:src="'https://www.youtube.com/embed/' + vid + '?autoplay=1&rel=0'"
						title="Video JDIH"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen>
					</iframe>
				</template>
			</div>

			<p class="mt-3 text-center text-sm text-white/80" x-text="vtitle"></p>
		</div>
	</div>
</section>
