<div class="bg-linear-to-b from-white via-slate-50 to-slate-100">
	<x-frontend.breadcrumb title="Berita" :listNav="[['label' => 'Berita', 'route' => route('frontend.berita.index')], ['label' => 'Detail']]" />

	<div class="border-b-2 border-b-gray-200">
		<div class="max-w-5xl mx-auto px-3 lg:px-6 py-4 flex items-center">

			<a wire:navigate.hover href="{{ url()->previous() }}"
				class="inline-flex items-center gap-2 text-sm font-medium
          text-slate-600 hover:text-black transition">
				<i class="fa-solid fa-arrow-left text-sm"></i>
				Kembali
			</a>
		</div>
	</div>

	<!-- Main -->
	<section class="pt-10 pb-18">

		<div class="max-w-5xl mx-auto px-0 lgp:x-4">

			<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

				<!-- ===================== -->
				<!-- KONTEN BERITA (KIRI) -->
				<!-- ===================== -->
				<article class="lg:col-span-8">
					<div class="bg-white rounded shadow-sm overflow-hidden">

						<!-- Gambar -->
						@php
							$image = checkFilePath(config('app.img_directory'), $data->image)
							    ? asset('storage/' . config('app.img_directory') . $data->image)
							    : asset('assets/img/default-img.jpg');
						@endphp
						<img src="{{ $image }}"
							alt="cover.png"
							class="w-full object-cover px-2 lg:px-0">

						<div class="p-8">
							<!-- Judul -->
							<h1 class="text-lg lg:text-2xl leading-6 lg:leading-8 font-bold text-slate-900">
								{{ $data->judul }}
							</h1>

							<!-- TANGGAL -->
							<span class="text-xs lg:text-sm text-slate-500 mt-3 flex items-center gap-1">
								<i class="fa-regular fa-calendar"></i>&nbsp;
								{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
							</span>

							<!-- Konten -->
							<div class="prose max-w-none mt-6">
								{!! $data->isi !!}
							</div>
						</div>
					</div>
				</article>

				<!-- ===================== -->
				<!-- SIDEBAR (KANAN) -->
				<!-- ===================== -->
				<aside class="lg:col-span-4 space-y-8 sticky top-24 self-start">

					<!-- SEARCH -->
					<div class="bg-white rounded shadow-sm p-6">
						<h3 class="font-semibold text-slate-900 mb-4">Cari Berita</h3>

						<form action="{{ route('frontend.berita.index') }}" method="GET" class="flex">
							<input
								type="text"
								name="q"
								value="{{ request('q') }}"
								placeholder="Search..."
								class="w-full rounded-l border border-slate-300 px-4 py-2 text-sm
                     focus:outline-none focus:ring-0 focus:border-primary transition">

							<button
								type="submit"
								class="bg-primary text-white px-4 rounded-r
                hover:bg-primary-hover transition
                  flex items-center justify-center">
								<i class="fa-solid fa-magnifying-glass text-sm"></i>
							</button>
						</form>
					</div>

					<!-- BERITA TERBARU -->
					<div class="bg-white rounded shadow-sm p-6">
						<h3 class="font-semibold text-slate-900 mb-5">Berita Terbaru</h3>

						<ul class="space-y-5">
							@forelse ($beritaTerbaru as $item)
								<li class="flex gap-4">
									@php
										$image = checkFilePath(config('app.img_directory'), $item->image)
										    ? asset('storage/' . config('app.img_directory') . $item->image)
										    : asset('assets/img/default-img.jpg');
									@endphp
									<img src="{{ $image }}"
										alt="{{ $item->judul }}"
										class="w-20 h-16 rounded object-cover object-center">

									<div>
										<a wire:navigate.hover href="{{ route('frontend.berita.show', Hashids::encode($item->id)) }}"
											class="text-sm font-medium text-slate-800 transition
                            hover:text-orange-600 line-clamp-2">
											{{ $item->judul }}
										</a>

										<!-- TANGGAL -->
										<span class="text-xs text-slate-500 mt-1 flex items-center gap-1">
											<i class="fa-regular fa-calendar"></i>
											{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
										</span>
									</div>

								</li>
							@empty
								<li class="text-sm text-slate-500 text-center">
									Belum ada berita terbaru.
								</li>
							@endforelse
						</ul>
					</div>

				</aside>

			</div>

		</div>
	</section>

</div>
