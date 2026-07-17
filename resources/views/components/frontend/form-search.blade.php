<form wire:submit.prevent="search">
	<div class="flex items-center lg:gap-3">
		<div class="relative flex-1">
			<svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
				fill="none" stroke="currentColor" stroke-width="2"
				viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round"
					d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
			</svg>

			<input type="text"
				wire:model.defer="q"
				placeholder="{{ $placeholder ?? 'Pencarian' }}..."
				class="text-xs lg:text-sm w-full pl-12 pr-4 py-3.5 lg:py-4 rounded border border-gray-300 focus:ring-none focus:outline-none text-gray-700"
				autofocus
				autocomplete="off">
		</div>

		<button type="submit"
			class="text-xs lg:text-sm px-8 py-3.5 lg:py-4 rounded bg-primary hover:bg-primary-hover text-white font-semibold hover:primary-hover transition">
			Cari
		</button>
	</div>
</form>
