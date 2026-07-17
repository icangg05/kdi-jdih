<div>
	<x-frontend.breadcrumb title="Informasi Hukum" :listNav="[['label' => 'Informasi Hukum']]" />


	<section class="bg-linear-to-b from-white via-slate-50 to-slate-100  py-12 lg:py-14">
		<div class="max-w-5xl mx-auto px-2 lg:px-0">

			<div class="mb-12">
				<x-frontend.form-search placeholder="Cari informasi hukum lainnya" />
			</div>


			<!-- LOADING SPINNER -->
			<div wire:loading wire:target="search" class="py-3 w-full">
				<div class="text-sm lg:text-base text-center text-gray-400">
					<i class="fa-solid fa-spinner fa-spin text-[20.5px]"></i>
				</div>
			</div>

			<div class="space-y-8"
				wire:loading.remove
				wire:target="search">
				@forelse ($data as $v)
					@php
						$image = checkFilePath(config('app.img_directory'), $v->image)
						    ? asset('storage/' . config('app.img_directory') . $v->image)
						    : asset('assets/img/default-img.jpg');
					@endphp

					<article
						class="group relative bg-white rounded-2xl border border-slate-200 overflow-hidden
					transition duration-300 hover:-translate-y-1 hover:shadow-xl">

						<div class="flex flex-col md:flex-row">

							<!-- Image -->
							<div class="relative md:w-72 shrink-0">
								<img
									src="{{ $image }}"
									alt="Pengumuman JDIH"
									class="h-48 md:h-full w-full object-cover" />

								<!-- Gradient Overlay -->
								<div
									class="absolute inset-0 bg-linear-to-t from-black/40 via-black/10 to-transparent md:hidden">
								</div>
							</div>

							<!-- Content -->
							<div class="flex flex-col justify-between p-6 lg:p-8 flex-1">

								<div>
									<!-- Badge -->
									<span
										class="inline-flex items-center gap-1 mb-4 rounded-full
									bg-orange-50 text-primary text-xs font-semibold px-3 py-1">
										{{ $v->jenis }}
									</span>

									<h3
										class="text-base lg:text-lg font-semibold text-slate-900 leading-snug
									group-hover:text-primary transition">
										{{ Str::limit($v->judul, 60) }}
									</h3>

									<p
										class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-2">
										{{ Str::limit(strip_tags($v->isi), 130) }}
									</p>
								</div>

								<div class="mt-6 flex gap-3 items-center justify-between">
									<span class="text-xs lg:text-sm text-slate-500 flex items-center gap-1">
										📅 {{ Carbon\Carbon::parse($v->tanggal)->translatedFormat('d F Y') }}
									</span>

									<a wire:navigate.hover href="{{ route('frontend.informasi-hukum.show', Hashids::encode($v->id)) }}"
										class="inline-flex items-center gap-1 text-xs lg:text-sm font-semibold text-primary
									group-hover:gap-2 transition-all">
										Baca Selengkapnya
										<span>→</span>
									</a>
								</div>

							</div>
						</div>
					</article>
				@empty
					<div class="text-sm lg:text-base text-center text-gray-400">
						Tidak ada data ditemukan.
						@if ($q)
							<span class="italic">Kata kunci : {{ $q }}</span>
						@endif
					</div>
				@endforelse
			</div>

			<div class="mt-6"
				wire:loading.remove
				wire:target="search">
				{{ $data->links() }}
			</div>
		</div>
	</section>
</div>
