<div>
	<x-frontend.breadcrumb :title="__('Video')" :listNav="[['label' => __('Video')]]" />

	<section
		x-data="{ open: false, vid: '', vtitle: '' }"
		class="relative bg-linear-to-b from-white to-gray-50 py-12 lg:py-16">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
			<div class="absolute inset-0 opacity-60"
				style="background-image: radial-gradient(rgba(1,91,165,0.06) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%); mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%);"></div>
			<i class="fa-solid fa-play absolute right-[6%] top-40 text-[8rem] text-accent/4 -rotate-6"></i>
			<i class="fa-solid fa-video absolute -left-6 bottom-16 text-[8rem] text-primary/4 rotate-6"></i>
		</div>

		<div class="relative z-10 max-w-5xl mx-auto px-4 lg:px-0">

			<x-frontend.form-search :placeholder="__('Cari video lainnya')" :live="true" />

			<!-- Info jumlah hasil -->
			<div class="mt-6 flex items-center justify-between gap-3"
				wire:loading.remove
				wire:target="search">
				<p class="text-xs lg:text-sm text-gray-500">
					<span class="font-semibold text-accent">{{ $data->total() }}</span> {{ __('video') }}
					@if ($q)
						<span class="text-gray-400">· {{ __('Kata kunci') }} "<span class="italic">{{ $q }}</span>"</span>
					@endif
				</p>
				<span class="hidden sm:inline-block h-px w-24 bg-linear-to-r from-primary/60 to-transparent"></span>
			</div>

			<!-- LOADING SKELETON -->
			<div wire:loading wire:target="search" class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
				@for ($i = 0; $i < 6; $i++)
					<div class="rounded overflow-hidden ring-1 ring-slate-200 bg-white animate-pulse">
						<div class="aspect-video bg-slate-100"></div>
						<div class="p-5 space-y-3">
							<div class="h-4 w-3/4 rounded bg-slate-100"></div>
							<div class="h-3 w-1/3 rounded bg-slate-100 mt-4"></div>
						</div>
					</div>
				@endfor
			</div>

			<!-- CONTENT -->
			<div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3"
				wire:loading.remove
				wire:target="search">
				@forelse ($data as $v)
					<x-frontend.cards.video-card :item="$v" :delay="$loop->index * 0.06" />
				@empty
					<div class="col-span-full">
						<div class="mx-auto max-w-md text-center py-16 px-6 rounded border border-dashed border-gray-200 bg-white">
							<div class="mx-auto flex h-14 w-14 items-center justify-center rounded bg-accent/10 text-accent">
								<i class="fa-solid fa-video text-2xl"></i>
							</div>
							<p class="mt-4 font-semibold text-gray-700">{{ __('Tidak ada data ditemukan.') }}</p>
							@if ($q)
								<p class="mt-1 text-sm text-gray-400">{{ __('Kata kunci') }} : <span class="italic">{{ $q }}</span></p>
							@endif
						</div>
					</div>
				@endforelse
			</div>

			<!-- Pagination -->
			<div class="mt-10"
				wire:loading.remove
				wire:target="search">
				{{ $data->links() }}
			</div>
		</div>

		<!-- MODAL VIDEO (sama seperti section-video) -->
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
</div>
