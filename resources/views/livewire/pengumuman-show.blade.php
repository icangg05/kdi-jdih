<div class="bg-linear-to-b from-white via-slate-50 to-slate-100">
	<x-frontend.breadcrumb
		title="Pengumuman"
		:listNav="[['label' => 'Pengumuman', 'route' => route('frontend.pengumuman.index')], ['label' => 'Detail']]" />

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

		<div class="max-w-5xl mx-auto px-0 lg:px-4">

			<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

				<!-- ===================== -->
				<!-- KONTEN BERITA (KIRI) -->
				<!-- ===================== -->
				<article class="lg:col-span-8 space-y-6 px-3 lg:px-0">

					<!-- Badge -->
					<span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded lg:rounded-full text-xs font-semibold">
						{{ $data->tag }}
					</span>

					<!-- Title -->
					<h1 class="text-xl lg:text-2xl font-bold leading-6 lg;leading-7 text-slate-900">
						{{ $data->judul }}
					</h1>

					<!-- Date -->
					<div class="flex items-center gap-2 text-slate-600">
            <span class="text-sm">
              <i class="fa-regular fa-calendar text-sm"></i>&nbsp;
							{{ Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
							</p>
						</span>
					</div>

					<hr class="border-slate-200">

					<!-- Thumbnail + Deskripsi + Download -->
					<div class="grid grid-cols-1 md:grid-cols-12 gap-6">

						<div class="md:col-span-4">
							@php
								$image = checkFilePath(config('app.img_directory'), $data->image)
								    ? asset('storage/' . config('app.img_directory') . $data->image)
								    : asset('assets/img/default-img.jpg');
							@endphp

							<img src="{{ $image }}"
								class="rounded shadow object-cover w-full">

							<form action="{{ route('download_file') }}" method="POST">
								@csrf
								<input type="hidden" name="filePath"
									value="{{ config('app.doc_directory') . $data->dokumen }}">
								<button @disabled(!checkFilePath(config('app.doc_directory'), $data->dokumen))
									onclick="window.location='{{ $data->dokumen ? route('download_file', $data->dokumen) : '#' }}'"
									style="opacity: {{ !checkFilePath(config('app.doc_directory'), $data->dokumen) ? '.4' : '1' }};"
									class="inline-flex items-center gap-2 bg-primary text-white text-xs px-4 py-2 rounded mt-5 font-semibold hover:bg-primary-hover transition">
									<i class="fa-solid fa-file-lines"></i>
									DOWNLOAD
								</button>
							</form>
						</div>

						<div class="md:col-span-8 space-y-3">
							<div class="prose prose-sm max-w-none">
								{!! $data->isi !!}
							</div>
						</div>

					</div>

					<!-- PDF Preview -->
					@if (checkFilePath(config('app.doc_directory'), $data->dokumen))
						<div class="mt-8 bg-white rounded shadow border">
							<iframe
								src="{{ asset('storage/' . config('app.doc_directory') . $data->dokumen) }}"
								class="w-full h-125 lg:h-150 rounded-b"
								frameborder="0">
							</iframe>
						</div>
					@endif
				</article>

				<!-- ===================== -->
				<!-- SIDEBAR (KANAN) -->
				<!-- ===================== -->
				<aside class="lg:col-span-4 space-y-8 sticky top-24 self-start">

					<!-- SEARCH -->
					<div class="bg-white rounded shadow-sm p-6">
						<h3 class="font-semibold text-slate-900 mb-4">Cari Pengumuman</h3>

						<form action="{{ route('frontend.pengumuman.index') }}" method="GET" class="flex">
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

					<!-- PENGUMUMAN TERBARU -->
					<div class="bg-white rounded shadow-sm p-6">
						<h3 class="font-semibold text-slate-900 mb-5">Pengumuman Terbaru</h3>

						<ul class="space-y-5">
							@forelse ($pengumumanTerbaru as $item)
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
										<a wire:navigate.hover href="{{ route('frontend.pengumuman.show', Hashids::encode($item->id)) }}"
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
									Belum ada data terbaru.
								</li>
							@endforelse
						</ul>
					</div>

				</aside>
			</div>

		</div>
	</section>

</div>
