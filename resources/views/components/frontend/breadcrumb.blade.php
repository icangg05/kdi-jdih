<section class="relative bg-gray-900">
	<div class="absolute inset-0">
		<img src="{{ asset('assets/img/header.jpg') }}"
			alt="Sejarah"
			class="w-full h-full object-cover object-center opacity-15">
	</div>

	<div class="relative max-w-7xl mx-auto px-6 pt-30 lg:pt-37 py-14 lg:py-20 text-center">
		<h1 class="text-2xl md:text-4xl font-bold text-white">
			{{ $title }}
		</h1>
		<nav class="mt-2 lg:mt-4 text-xs lg:text-sm text-gray-300">
			<a wire:navigate.hover
				href="{{ route('frontend.beranda') }}"
				class="text-white hover:text-orange-400 transition">
				Home
			</a>

			@foreach ($listNav as $item)
				<span class="mx-2">›</span>

				@if ($loop->last)
					{{-- Item terakhir: selalu ORANGE --}}
					@if (!empty($item['route']))
						<a wire:navigate.hover
							href="{{ $item['route'] }}"
							class="text-orange-400 font-medium">
							{{ $item['label'] }}
						</a>
					@else
						<span class="text-orange-400 font-medium">
							{{ $item['label'] }}
						</span>
					@endif
				@else
					{{-- Bukan item terakhir --}}
					@if (!empty($item['route']))
						<a wire:navigate.hover
							href="{{ $item['route'] }}"
							class="text-white hover:text-orange-400 transition">
							{{ $item['label'] }}
						</a>
					@else
						<span class="text-white">
							{{ $item['label'] }}
						</span>
					@endif
				@endif
			@endforeach
		</nav>
	</div>
</section>
