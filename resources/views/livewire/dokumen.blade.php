<div>
	@php
		$jenisOptions = collect($tipeDokumen)
			->map(fn($t) => ['value' => $t->name, 'label' => Str::title($t->name)])
			->values()
			->all();

		$statusOptions = [
			['value' => 'dicabut', 'label' => __('Dicabut')],
			['value' => 'mencabut', 'label' => __('Mencabut')],
			['value' => 'diubah', 'label' => __('Diubah')],
			['value' => 'mengubah', 'label' => __('Mengubah')],
			['value' => 'Tidak memiliki daya guna', 'label' => __('Tidak memiliki daya guna')],
		];

		$hasFilter = $q !== '' || $jenis !== '' || $tahun !== '' || $status !== '' || $nomor !== '';
	@endphp

	<x-frontend.breadcrumb :title="$title" :listNav="[['label' => $title]]" />

	<section class="relative bg-linear-to-b from-white to-gray-50 py-12 lg:py-16">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			{{-- blob brand --}}
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
			{{-- grid titik halus, di-mask agar memudar --}}
			<div class="absolute inset-0 opacity-60"
				style="background-image: radial-gradient(rgba(1,91,165,0.06) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 25%, black, transparent 80%); mask-image: radial-gradient(ellipse 70% 55% at 50% 25%, black, transparent 80%);"></div>
			{{-- garis tipis atas --}}
			<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-slate-200 to-transparent"></div>
			{{-- watermark ikon hukum --}}
			<i class="fa-solid fa-scale-balanced absolute right-[5%] top-44 text-[9rem] text-accent/4 -rotate-6"></i>
			<i class="fa-solid fa-gavel absolute -left-6 bottom-16 text-[8rem] text-primary/4 rotate-12"></i>
			<i class="fa-regular fa-file-lines absolute right-[18%] bottom-24 text-[6rem] text-slate-900/3 rotate-6"></i>
		</div>

		<div class="relative z-10 max-w-5xl mx-auto px-4 lg:px-0">

			<x-frontend.form-search :placeholder="__('Cari dokumen hukum lainnya')" :live="true" />

			<div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
				<div class="lg:col-span-8">

					<!-- Info jumlah hasil -->
					<div class="mb-6 flex items-center justify-between gap-3"
						wire:loading.remove
						wire:target="search">
						<p class="text-xs lg:text-sm text-gray-500">
							<span class="font-semibold text-accent">{{ $data->total() }}</span> {{ __('dokumen') }}
							@if ($q)
								<span class="text-gray-400">· {{ __('Kata kunci') }} "<span class="italic">{{ $q }}</span>"</span>
							@endif
						</p>
						<span class="hidden sm:inline-block h-px w-20 bg-linear-to-r from-primary/60 to-transparent"></span>
					</div>

					<!-- LOADING SKELETON -->
					<div wire:loading wire:target="search" class="space-y-6">
						@for ($i = 0; $i < 3; $i++)
							<div class="rounded ring-1 ring-slate-200 bg-white p-6 animate-pulse">
								<div class="h-5 w-40 rounded bg-slate-100"></div>
								<div class="mt-4 h-4 w-3/4 rounded bg-slate-100"></div>
								<div class="mt-2 h-4 w-2/3 rounded bg-slate-100"></div>
								<div class="mt-4 flex gap-5">
									<div class="h-32 w-32 rounded bg-slate-100"></div>
									<div class="flex-1 space-y-2 pt-3">
										<div class="h-9 w-full rounded bg-slate-100"></div>
										<div class="h-9 w-full rounded bg-slate-100"></div>
									</div>
								</div>
							</div>
						@endfor
					</div>

					<div class="space-y-6"
						wire:loading.remove
						wire:target="search">
						@forelse ($data as $v)
							@php
								$hashId = Hashids::encode($v->id);
							@endphp

							<article
								style="animation-delay: {{ $loop->index * 0.07 }}s"
								class="animate-rise group relative overflow-hidden bg-white rounded ring-1 ring-slate-200 p-6
									transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 hover:ring-primary/30">

								<!-- garis aksen atas saat hover -->
								<span aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

								<span
									class="inline-flex items-center gap-2 mb-3 text-xs font-semibold
									text-primary bg-primary/10 px-3 py-1 rounded">
									<i class="fa-solid fa-scale-balanced"></i>
									@if ($kategori === 'monografi' || $kategori === 'artikel')
										TAHUN TERBIT ({{ $v->tahun_terbit }})
									@else
										{{ $v->bentuk_peraturan }} • ({{ $v->tahun_terbit }})
									@endif
								</span>

								<a wire:navigate.hover href="{{ route('frontend.dokumen.show', [$kategori, $hashId]) }}"
									title="{{ tt($v, 'judul') }}"
									class="text-sm lg:text-base leading-6 font-semibold text-slate-900
									group-hover:text-primary transition line-clamp-3">
									{{ tt($v, 'judul') }}
								</a>

								<div class="mt-4 flex flex-wrap items-start gap-5">

									{{-- QR Code --}}
									<div class="flex flex-col items-center text-xs text-slate-500">
										<div class="w-32 border border-slate-200 rounded p-1 bg-white shadow-sm">
											<div id="qrcode-{{ $hashId }}">
												{!! QrCode::format('svg')->size(118.5)->generate(route('frontend.dokumen.show', [$kategori, $hashId])) !!}
											</div>
										</div>

										<button type="button"
											class="mt-1.5 inline-flex items-center gap-1.5 cursor-pointer hover:text-primary transition"
											onclick="downloadPNG('{{ $hashId }}')">
											<i class="fa-solid fa-cloud-arrow-down opacity-60"></i>
											Download QR
										</button>
									</div>

									<div class="flex flex-col gap-2 mt-2 min-w-45 flex-1">
										<form action="{{ route('download_file') }}" method="POST">
											@csrf
											<input type="hidden" name="filePath"
												value="{{ config('app.doc_directory') . $v->dokumen_lampiran }}">
											<button @disabled(!checkFilePath(config('app.doc_directory'), $v->dokumen_lampiran))
												onclick="window.location='{{ $v->dokumen_lampiran ? route('download_file', $v->dokumen_lampiran) : '#' }}'"
												style="opacity: {{ !checkFilePath(config('app.doc_directory'), $v->dokumen_lampiran) ? '.4' : '1' }};"
												class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded
													bg-accent text-white text-xs font-semibold
													hover:bg-accent-hover transition shadow-sm shadow-accent/20 w-full
													focus:outline-none focus:ring-4 focus:ring-accent/20">
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
												class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded
													bg-primary text-white text-xs font-semibold
													hover:bg-primary-hover transition shadow-sm shadow-primary/20 w-full
													focus:outline-none focus:ring-4 focus:ring-primary/20">
												<i class="fa-solid fa-file-arrow-down"></i>
												Abstrak
											</button>
										</form>
									</div>
								</div>
							</article>
						@empty
							<div class="text-center py-16 px-6 rounded border border-dashed border-gray-200 bg-white">
								<div class="mx-auto flex h-14 w-14 items-center justify-center rounded bg-accent/10 text-accent">
									<i class="fa-regular fa-folder-open text-2xl"></i>
								</div>
								<p class="mt-4 font-semibold text-gray-700">{{ __('Tidak ada data ditemukan.') }}</p>
								@if ($q)
									<p class="mt-1 text-sm text-gray-400">{{ __('Kata kunci') }} : <span class="italic">{{ $q }}</span></p>
								@endif
							</div>
						@endforelse
					</div>
				</div>

				<!-- Sidebar search -->
				<div class="lg:col-span-4">
					<form wire:submit.prevent="search"
						class="bg-white border border-slate-200 rounded shadow-sm shadow-slate-200/60 sticky top-22">

						{{-- Aksen atas --}}
						<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 rounded-t bg-linear-to-r from-primary via-primary/60 to-accent"></div>

						<div class="p-6 space-y-5">

							{{-- Header --}}
							<div class="flex items-center gap-3 border-b border-slate-100 pb-4">
								<div class="flex h-10 w-10 items-center justify-center rounded bg-linear-to-br from-primary to-primary-hover text-white shadow-sm shadow-primary/30">
									<i class="fa-solid fa-sliders"></i>
								</div>
								<div>
									<h4 class="text-base font-semibold text-slate-900 leading-tight">
										{{ __('Filter Dokumen') }}
									</h4>
									<p class="text-xs text-gray-400">{{ __('Persempit hasil pencarian') }}</p>
								</div>
							</div>

							{{-- Jenis Dokumen --}}
							<x-frontend.select-search
								name="jenis"
								:label="'Jenis ' . ucfirst($kategori)"
								:placeholder="'Pilih Jenis ' . ucfirst($kategori)"
								icon="fa-solid fa-layer-group"
								:options="$jenisOptions"
								:searchPlaceholder="__('Cari jenis...')" />

							{{-- Nomor + Tahun (satu baris untuk hemat ruang) --}}
							<div class="@if ($kategori == 'peraturan') grid grid-cols-2 gap-3 @endif">
								@if ($kategori == 'peraturan')
									<div class="space-y-1.5">
										<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
											{{ __('Nomor') }}
										</label>
										<div class="relative">
											<i class="fa-solid fa-hashtag pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
											<input type="text"
												wire:model.live.debounce.400ms="nomor"
												placeholder="cth: 8"
												class="w-full pl-9 pr-3 py-2.5 rounded border border-gray-300 text-sm transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10">
										</div>
									</div>
								@endif

								<div class="space-y-1.5">
									<label class="text-xs font-medium text-gray-500 uppercase tracking-wide">
										{{ __('Tahun') }}
									</label>
									<div class="relative">
										<i class="fa-solid fa-calendar pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
										<input type="text"
											wire:model.live.debounce.400ms="tahun"
											placeholder="cth: {{ date('Y') }}"
											class="w-full pl-9 pr-3 py-2.5 rounded border border-gray-300 text-sm transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10">
									</div>
								</div>
							</div>

							{{-- Status --}}
							@if ($kategori == 'peraturan')
								<x-frontend.select-search
									name="status"
									:label="__('Status')"
									:placeholder="__('Pilih Status')"
									icon="fa-solid fa-circle-check"
									:options="$statusOptions"
									:searchPlaceholder="__('Cari status...')" />
							@endif

							{{-- Reset (aktif hanya bila ada filter/pencarian aktif) --}}
							<button
								type="button"
								wire:click="resetFilter"
								@disabled(!$hasFilter)
								class="w-full flex items-center justify-center gap-2 py-2.5 rounded text-xs font-semibold transition focus:outline-none
									{{ $hasFilter
										? 'bg-primary text-white hover:bg-primary-hover focus:ring-4 focus:ring-primary/25'
										: 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
								<i class="fa-solid fa-rotate-left"></i>
								{{ __('Reset Filter') }}
							</button>

						</div>
					</form>
				</div>
			</div>

			<div class="mt-10"
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
