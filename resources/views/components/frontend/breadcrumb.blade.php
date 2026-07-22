<section class="relative overflow-hidden bg-[#012a4d]">
	{{-- Foto header sebagai background utama --}}
	<div class="absolute inset-0">
		<img src="{{ asset('assets/img/header.jpg') }}"
			alt=""
			aria-hidden="true"
			class="w-full h-full object-cover object-center">
	</div>

	{{-- Overlay brand navy untuk kedalaman & keterbacaan teks (gambar tetap tembus) --}}
	<div class="absolute inset-0 bg-linear-to-br from-[#012a4d]/85 via-[#013a6b]/78 to-[#012036]/90"></div>

	{{-- Garis aksen oranye tipis di bawah --}}
	<div class="absolute inset-x-0 bottom-0 h-1 bg-linear-to-r from-transparent via-primary to-transparent opacity-70"></div>

	<div class="relative max-w-7xl mx-auto px-6 pt-30 lg:pt-37 py-14 lg:py-20 text-center animate-rise">
		<h1 class="text-3xl md:text-4xl lg:text-[2.75rem] font-bold tracking-tight text-white leading-tight">
			{{ $title }}
		</h1>

		<span class="mt-4 lg:mt-5 inline-block h-1 w-14 rounded bg-primary"></span>

		<nav aria-label="Breadcrumb"
			class="mt-4 lg:mt-5 flex items-center justify-center flex-wrap gap-x-1.5 gap-y-1 text-xs lg:text-sm font-medium">
			<a wire:navigate.hover
				href="{{ route('frontend.beranda') }}"
				class="inline-flex items-center gap-1.5 text-white/70 hover:text-white transition-colors">
				<svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10" />
				</svg>
				{{ __('Home') }}
			</a>

			@foreach ($listNav as $item)
				<svg class="w-3.5 h-3.5 text-white/35 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
				</svg>

				@if ($loop->last)
					@if (!empty($item['route']))
						<a wire:navigate.hover
							href="{{ $item['route'] }}"
							aria-current="page"
							class="text-primary">
							{{ $item['label'] }}
						</a>
					@else
						<span aria-current="page" class="text-primary">
							{{ $item['label'] }}
						</span>
					@endif
				@else
					@if (!empty($item['route']))
						<a wire:navigate.hover
							href="{{ $item['route'] }}"
							class="text-white/70 hover:text-white transition-colors">
							{{ $item['label'] }}
						</a>
					@else
						<span class="text-white/70">
							{{ $item['label'] }}
						</span>
					@endif
				@endif
			@endforeach
		</nav>
	</div>
</section>
