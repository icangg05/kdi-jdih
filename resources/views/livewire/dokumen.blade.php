<div>
	<x-frontend.breadcrumb :title="$title" :listNav="[['label' => $title]]" />

	<section class="bg-linear-to-b from-white via-slate-50 to-slate-100 py-12 lg:py-14">
		<div class="max-w-5xl mx-auto px-2 lg:px-0">

			<x-frontend.form-search placeholder="Cari dokumen hukum lainnya" />

			<div class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
				<div class="lg:col-span-8">
					<!-- LOADING SPINNER -->
					<div wire:loading wire:target="search" class="py-3 w-full">
						<div class="text-sm lg:text-base text-center text-gray-400">
							<i class="fa-solid fa-spinner fa-spin text-[20.5px]"></i>
						</div>
					</div>

					<div class="space-y-6"
						wire:loading.remove
						wire:target="search">
						@forelse ($data as $v)
							@php
								$hashId = Hashids::encode($v->id);
							@endphp

							<div
								class="group bg-white rounded border border-gray-200 p-6
                hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">

								<span
									class="inline-flex items-center gap-2 mb-3 text-xs font-semibold
                text-primary bg-orange-50 px-3 py-1 rounded-full">
									<i class="fa-solid fa-scale-balanced"></i>
									@if ($kategori === 'monografi' || $kategori === 'artikel')
										TAHUN TERBIT ({{ $v->tahun_terbit }})
									@else
										{{ $v->bentuk_peraturan }} • ({{ $v->tahun_terbit }})
									@endif
								</span>

								<a wire:navigate.hover href="{{ route('frontend.dokumen.show', [$kategori, $hashId]) }}"
									class="text-sm lg:text-base leading-5 font-semibold text-gray-900
                group-hover:text-primary transition line-clamp-3">
									{{ $v->judul }}
								</a>

								<div class="mt-3.5 flex flex-wrap items-start gap-5">

									{{-- QR Code --}}
									<div class="flex flex-col items-center text-xs text-gray-500">
										<div class="w-32 border border-black/10 rounded p-1 bg-white shadow-sm">
											<div id="qrcode-{{ $hashId }}">
												{!! QrCode::format('svg')->size(118.5)->generate(route('frontend.dokumen.show', [$kategori, $hashId])) !!}
											</div>
										</div>

										<button type="button"
											class="mt-1 flex items-center gap-1.5 cursor-pointer hover:text-primary transition"
											onclick="downloadPNG('{{ $hashId }}')">
											<i class="fa-solid fa-cloud-arrow-down opacity-60"></i>
											Download QR
										</button>
									</div>

									<div class="flex flex-col gap-2 mt-3">
										<form action="{{ route('download_file') }}" method="POST">
											@csrf
											<input type="hidden" name="filePath"
												value="{{ config('app.doc_directory') . $v->dokumen_lampiran }}">
											<button @disabled(!checkFilePath(config('app.doc_directory'), $v->dokumen_lampiran))
												onclick="window.location='{{ $v->dokumen_lampiran ? route('download_file', $v->dokumen_lampiran) : '#' }}'"
												style="opacity: {{ !checkFilePath(config('app.doc_directory'), $v->dokumen_lampiran) ? '.4' : '1' }};"
												class="inline-flex items-center gap-2 px-5 py-2 rounded
                        bg-accent text-white text-xs font-semibold
                        hover:bg-accent-hover transition shadow-sm w-full">
												<i class="fa-solid fa-file-arrow-down"></i>
												Download
											</button>
										</form>

										<form action="{{ route('download_file') }}" method="POST">
											@csrf
											<input type="hidden" name="filePath"
												value="{{ config('app.doc_directory') . $v->abstrak }}">
											<button @disabled(!checkFilePath(config('app.doc_directory'), $v->abstrak))
												onclick="window.location='{{ $v->abstrak ? route('download_file', $v->abstrak) : '#' }}'"
												style="opacity: {{ !checkFilePath(config('app.doc_directory'), $v->abstrak) ? '.4' : '1' }};"
												class="inline-flex items-center gap-2 px-5 py-2 rounded
                        bg-primary text-white text-xs font-semibold
                        hover:bg-primary-hover transition shadow-sm w-full">
												<i class="fa-solid fa-file-arrow-down"></i>
												Abstrak
											</button>
										</form>
									</div>
								</div>
							</div>
						@empty
							<div class="text-center text-gray-400">
								Tidak ada data ditemukan.
								@if ($q)
									<span class="italic">Kata kunci : {{ $q }}</span>
								@endif
							</div>
						@endforelse
					</div>
				</div>

				<!-- Sidebar search -->
				<div class="lg:col-span-4">
					<form wire:submit.prevent="search"
						class="bg-white/90 backdrop-blur border border-gray-200 rounded p-6 shadow-sm space-y-4 sticky top-22">

						{{-- Header --}}
						<div class="flex items-center gap-3 border-b border-b-black/20 pb-4">
							<div
								class="w-9 h-9 flex items-center justify-center
                       rounded bg-orange-100 text-primary">
								<i class="fa-solid fa-filter"></i>
							</div>
							<h4 class="text-base font-semibold text-gray-900">
								Filter Dokumen
							</h4>
						</div>

						{{-- Jenis Dokumen --}}
						<div class="space-y-1">
							<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
								Jenis {{ $kategori }}
							</label>
							<div class="relative">
								<i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
								<select wire:model.defer="jenis"
									class="select2 w-full pl-11 pr-4 py-2.5 rounded border border-gray-300 text-sm focus:outline-none focus:border-primary">
									<option value="">Pilh Jenis {{ ucfirst($kategori) }}</option>
									@foreach ($tipeDokumen as $v)
										<option value="{{ $v->name }}">
											{{ Str::title($v->name) }}
										</option>
									@endforeach
								</select>
							</div>
						</div>

						{{-- Nomor --}}
						@if ($kategori == 'peraturan')
							<div class="space-y-1">
								<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
									Nomor Peraturan
								</label>
								<div class="relative">
									<i
										class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2
                           text-gray-400 text-sm"></i>
									<input type="text"
										wire:model.defer="nomor"
										placeholder="Contoh: 8"
										class="w-full pl-11 pr-4 py-2.5 rounded border border-gray-300
                              text-sm focus:outline-none focus:border-primary">
								</div>
							</div>
						@endif

						{{-- Tahun --}}
						<div class="space-y-1">
							<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
								Tahun Terbit
							</label>
							<div class="relative">
								<i
									class="fa-solid fa-calendar absolute left-4 top-1/2 -translate-y-1/2
                           text-gray-400 text-sm"></i>
								<input type="text"
									wire:model.defer="tahun"
									placeholder="Contoh: {{ date('Y') }}"
									value="{{ request()->tahun }}"
									class="w-full pl-11 pr-4 py-2.5 rounded border border-gray-300
                              text-sm focus:outline-none focus:border-primary">
							</div>
						</div>

						{{-- Status --}}
						@if ($kategori == 'peraturan')
							<div class="space-y-1">
								<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
									Status
								</label>
								<div class="relative">
									<i
										class="fa-solid fa-circle-check absolute left-4 top-1/2 -translate-y-1/2
                           text-gray-400 text-sm"></i>
									<select wire:model.defer="status"
										class="w-full pl-11 pr-4 py-2.5 rounded border border-gray-300
                           text-sm focus:outline-none focus:border-primary">
										<option value="">Pilih Status</option>
										<option value="dicabut">Dicabut</option>
										<option value="mencabut">Mencabut</option>
										<option value="diubah">Diubah</option>
										<option value="mengubah">Mengubah</option>
										<option value="Tidak memiliki daya guna">Tidak memiliki daya guna
										</option>
									</select>
								</div>
							</div>
						@endif

						{{-- Actions --}}
						<div class="flex gap-3 pt-2">
							<button
								class="flex-1 flex items-center justify-center gap-2
                       py-2.5 rounded bg-primary text-white
                       text-xs font-semibold hover:bg-primary-hover focus:outline-none focus:bg-primary-hover transition">
								<i class="fa-solid fa-magnifying-glass"></i>
								Terapkan
							</button>

							<button
								type="button"
								wire:click="resetFilter"
								class="flex items-center justify-center
                       px-4 rounded border border-gray-300
                       text-gray-600 hover:bg-gray-100 transition focus:outline-none focus:bg-gray-100">
								<i class="fa-solid fa-rotate-left"></i>
							</button>
						</div>

					</form>
				</div>
			</div>

			<div class="mt-6"
				wire:loading.remove
				wire:target="search">
				{{ $data->links() }}
			</div>
		</div>
	</section>


	<script>
		function downloadPNG(id) {
			const svg = document.querySelector('#qrcode-' + id + ' svg');
			const serializer = new XMLSerializer();
			const svgStr = serializer.serializeToString(svg);

			const canvas = document.createElement("canvas");
			const ctx = canvas.getContext("2d");

			const img = new Image();
			const svgBlob = new Blob([svgStr], {
				type: "image/svg+xml;charset=utf-8"
			});
			const url = URL.createObjectURL(svgBlob);

			img.onload = function() {
				const scale = 5;

				canvas.width = img.width * scale;
				canvas.height = img.height * scale;

				ctx.scale(scale, scale);
				ctx.drawImage(img, 0, 0);

				URL.revokeObjectURL(url);

				const pngUrl = canvas.toDataURL("image/png");

				const a = document.createElement("a");
				a.href = pngUrl;
				a.download = "qrcode-{{ $kategori }}-" + id + ".png";
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			};

			img.src = url;
		}
	</script>
</div>
