@props(['placeholder' => null, 'live' => false])

@php
    $wireModel = $live ? 'wire:model.live.debounce.400ms="q"' : 'wire:model.defer="q"';
@endphp

<form wire:submit.prevent="search">
	<div class="flex items-center gap-2 lg:gap-3">
		<div class="group relative flex-1">
			<svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 transition-colors group-focus-within:text-accent"
				fill="none" stroke="currentColor" stroke-width="2"
				viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round"
					d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
			</svg>

			<input type="text"
				{!! $wireModel !!}
				placeholder="{{ $placeholder ?? 'Pencarian' }}..."
				class="text-sm w-full pl-12 pr-4 py-3.5 lg:py-4 rounded border border-gray-200 bg-white text-gray-700 shadow-sm placeholder:text-gray-400 transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10"
				autofocus
				autocomplete="off">
		</div>

		@if ($live)
			{{-- Pencarian sudah live/debounce → tombol jadi Reset (aktif hanya bila ada kata kunci) --}}
			<button type="button"
				x-data
				wire:click="$set('q', '')"
				x-bind:disabled="!$wire.q"
				x-bind:class="$wire.q
					? 'bg-primary text-white hover:bg-primary-hover shadow-sm shadow-primary/20 focus:ring-4 focus:ring-primary/25 cursor-pointer'
					: 'bg-gray-100 text-gray-400 cursor-not-allowed'"
				class="inline-flex items-center gap-2 text-sm px-6 lg:px-8 py-3.5 lg:py-4 rounded font-semibold transition-colors focus:outline-none">
				<i class="fa-solid fa-rotate-left text-xs"></i>
				<span>Reset</span>
			</button>
		@else
			<button type="submit"
				class="inline-flex items-center gap-2 text-sm px-6 lg:px-8 py-3.5 lg:py-4 rounded bg-primary hover:bg-primary-hover text-white font-semibold shadow-sm shadow-primary/20 transition-colors focus:outline-none focus:ring-4 focus:ring-primary/25">
				<svg class="w-4 h-4 lg:hidden" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
				</svg>
				<span>Cari</span>
			</button>
		@endif
	</div>
</form>
