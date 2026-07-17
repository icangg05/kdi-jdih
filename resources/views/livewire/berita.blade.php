<div>
	<x-frontend.breadcrumb title="Berita" :listNav="[['label' => 'Berita']]" />

	<section class="bg-linear-to-b from-white via-slate-50 to-slate-100 py-12 lg:py-14">
		<div class="max-w-5xl mx-auto px-2 lg:px-0">

			<x-frontend.form-search placeholder="Cari berita lainnya" />


			<!-- LOADING SPINNER -->
			<div wire:loading wire:target="search" class="py-9 w-full">
				<div class="text-sm lg:text-base text-center text-gray-400">
					<i class="fa-solid fa-spinner fa-spin text-[20.5px]"></i>
				</div>
			</div>

			<!-- CONTENT -->
			<div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3"
				wire:loading.remove
				wire:target="search">
				@forelse ($data as $v)
					@php
						$image = checkFilePath(config('app.img_directory'), $v->image)
						    ? asset('storage/' . config('app.img_directory') . $v->image)
						    : asset('assets/img/default-img.jpg');
					@endphp

					<article
						class="group relative bg-white rounded-2xl overflow-hidden
              border border-slate-200
              transform-gpu will-change-transform
              transition-all duration-300 ease-out
              hover:-translate-y-1 hover:shadow-2xl">

						<!-- Image -->
						<div class="relative h-56 overflow-hidden">
							<img
								src="{{ $image }}"
								alt="Berita"
								class="absolute inset-0 w-full h-full object-cover
							group-hover:scale-105 transition-transform duration-500" />

							<!-- Overlay -->
							<div class="absolute inset-0 bg-linear-to-t from-black/50 via-black/10 to-transparent"></div>

							<!-- Date Badge -->
							<span
								class="absolute top-4 left-4 z-10
							rounded-full bg-primary/90 text-white
							text-xs font-semibold px-3 py-1 shadow">
								{{ Carbon\Carbon::parse($v->created_at)->translatedFormat('d F Y') }}
							</span>
						</div>

						<!-- Content -->
						<div class="p-6">
							<h3
								class="text-base lg:text-lg font-semibold text-slate-800
							leading-snug line-clamp-2
							group-hover:text-primary transition">
								{{ Str::limit($v->judul, 20) }}
							</h3>

							<p
								class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
								{{ Str::limit(strip_tags($v->isi), 100) }}
							</p>

							<a
								wire:navigate.hover href="{{ route('frontend.berita.show', Hashids::encode($v->id)) }}"
								class="inline-flex items-center gap-1 mt-5
							text-sm font-semibold text-primary
							group-hover:gap-2 transition-all">
								Baca Selengkapnya
								<span>→</span>
							</a>
						</div>

					</article>
				@empty
					<div class="text-center text-gray-400 col-span-3">
						Tidak ada data ditemukan.
						@if ($q)
							<span class="italic">Kata kunci : {{ $q }}</span>
						@endif
					</div>
				@endforelse
			</div>

			<!-- Pagination -->
			<div class="mt-6"
				wire:loading.remove
				wire:target="search">
				{{ $data->links() }}
			</div>
		</div>
	</section>
</div>
