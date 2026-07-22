<div>
	<x-frontend.breadcrumb :title="__('Berita')" :listNav="[['label' => __('Berita')]]" />

	<section class="bg-linear-to-b from-white to-gray-50 py-12 lg:py-16">
		<div class="max-w-5xl mx-auto px-4 lg:px-0">

			<x-frontend.form-search :placeholder="__('Cari berita lainnya')" :live="true" />

			<!-- Info jumlah hasil -->
			<div class="mt-6 flex items-center justify-between gap-3"
				wire:loading.remove
				wire:target="search">
				<p class="text-xs lg:text-sm text-gray-500">
					<span class="font-semibold text-accent">{{ $data->total() }}</span> {{ __('berita') }}
					@if ($q)
						<span class="text-gray-400">· {{ __('Kata kunci') }} "<span class="italic">{{ $q }}</span>"</span>
					@endif
				</p>
				<span class="hidden sm:inline-block h-px w-24 bg-linear-to-r from-primary/60 to-transparent"></span>
			</div>

			<!-- LOADING SKELETON -->
			<div wire:loading wire:target="search"
				class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
				@for ($i = 0; $i < 6; $i++)
					<div class="rounded overflow-hidden ring-1 ring-slate-200 bg-white animate-pulse">
						<div class="h-56 bg-slate-100"></div>
						<div class="p-6 space-y-3">
							<div class="h-4 w-3/4 rounded bg-slate-100"></div>
							<div class="h-3 w-full rounded bg-slate-100"></div>
							<div class="h-3 w-5/6 rounded bg-slate-100"></div>
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
					<x-frontend.cards.berita-card :item="$v" :delay="$loop->index * 0.08" />
				@empty
					<div class="col-span-full">
						<div class="mx-auto max-w-md text-center py-16 px-6 rounded border border-dashed border-gray-200 bg-white">
							<div class="mx-auto flex h-14 w-14 items-center justify-center rounded bg-accent/10 text-accent">
								<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h9l7 7v7a2 2 0 01-2 2zM8 8h4M8 12h8M8 16h8" />
								</svg>
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
	</section>
</div>
