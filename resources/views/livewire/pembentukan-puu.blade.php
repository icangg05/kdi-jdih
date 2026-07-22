<div>
	@php
		$hasFilter = $q !== '' || $tahun !== '' || $nomor !== '';
	@endphp

	<x-frontend.breadcrumb
		:title="__('Pembentukan PUU')"
		:listNav="[['label' => __('Pembentukan PUU')]]" />

	<section class="relative bg-linear-to-b from-white to-gray-50 py-12 lg:py-16">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
			<div class="absolute inset-0 opacity-60"
				style="background-image: radial-gradient(rgba(1,91,165,0.06) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%); mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%);"></div>
			<i class="fa-solid fa-file-pen absolute right-[6%] top-40 text-[9rem] text-accent/4 -rotate-6"></i>
			<i class="fa-solid fa-scale-balanced absolute -left-6 bottom-16 text-[8rem] text-primary/4 rotate-6"></i>
		</div>

		<div class="relative z-10 max-w-5xl mx-auto px-4 lg:px-0">

			<x-frontend.form-search :placeholder="__('Cari judul, penulis, lembaga, nomor...')" :live="true" />

			{{-- Tab kategori --}}
			<div class="mt-8 flex gap-2 overflow-x-auto pb-2 -mx-1 px-1">
				@foreach ($categories as $c)
					@php $active = $kategori === $c['value']; @endphp
					<button type="button"
						wire:click="selectKategori('{{ $c['value'] }}')"
						class="group inline-flex shrink-0 items-center gap-2 rounded px-3.5 py-2 text-xs lg:text-sm font-semibold transition
							{{ $active
								? 'bg-primary text-white shadow-sm shadow-primary/25'
								: 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-primary/40 hover:text-primary' }}">
						<i class="fa-solid {{ $c['icon'] }} text-[11px] {{ $active ? 'text-white' : 'text-primary' }}"></i>
						{{ $c['label'] }}
						<span class="inline-flex items-center justify-center rounded px-1.5 min-w-5 h-5 text-[10px] tabular-nums
							{{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
							{{ $counts[$c['db']] ?? 0 }}
						</span>
					</button>
				@endforeach
			</div>

			{{-- Filter tahun + nomor + reset --}}
			<div class="mt-4 flex flex-wrap items-center gap-3">
				<div class="relative">
					<i class="fa-solid fa-calendar pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
					<input type="text" wire:model.live.debounce.400ms="tahun"
						placeholder="{{ __('Tahun') }}"
						class="w-32 pl-9 pr-3 py-2.5 rounded border border-gray-300 bg-white text-sm transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10">
				</div>
				<div class="relative flex-1 min-w-45">
					<i class="fa-solid fa-hashtag pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
					<input type="text" wire:model.live.debounce.400ms="nomor"
						placeholder="{{ __('Nomor dokumen') }}"
						class="w-full pl-9 pr-3 py-2.5 rounded border border-gray-300 bg-white text-sm transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10">
				</div>
				<button type="button" wire:click="resetFilter" @disabled(!$hasFilter)
					class="inline-flex items-center gap-2 px-4 py-2.5 rounded text-xs font-semibold transition focus:outline-none
						{{ $hasFilter
							? 'bg-primary text-white hover:bg-primary-hover focus:ring-4 focus:ring-primary/25'
							: 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
					<i class="fa-solid fa-rotate-left"></i>
					{{ __('Reset') }}
				</button>
			</div>

			{{-- Info jumlah hasil --}}
			<div class="mt-6 flex items-center justify-between gap-3"
				wire:loading.remove wire:target="q,tahun,nomor,kategori,selectKategori,resetFilter">
				<p class="text-xs lg:text-sm text-gray-500">
					<span class="font-semibold text-accent">{{ $data->total() }}</span>
					{{ __('dokumen') }} · <span class="text-slate-600 font-medium">{{ $current['label'] ?? '' }}</span>
					@if ($q)
						<span class="text-gray-400">· "{{ $q }}"</span>
					@endif
				</p>
				<span class="hidden sm:inline-block h-px w-20 bg-linear-to-r from-primary/60 to-transparent"></span>
			</div>

			{{-- LOADING SKELETON --}}
			<div wire:loading wire:target="q,tahun,nomor,kategori,selectKategori,resetFilter"
				class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
				@for ($i = 0; $i < 6; $i++)
					<div class="rounded ring-1 ring-slate-200 bg-white p-6 animate-pulse">
						<div class="h-5 w-28 rounded bg-slate-100"></div>
						<div class="mt-4 h-4 w-3/4 rounded bg-slate-100"></div>
						<div class="mt-2 h-3 w-full rounded bg-slate-100"></div>
						<div class="mt-2 h-3 w-5/6 rounded bg-slate-100"></div>
						<div class="mt-6 h-9 w-full rounded bg-slate-100"></div>
					</div>
				@endfor
			</div>

			{{-- CONTENT --}}
			<div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
				wire:loading.remove wire:target="q,tahun,nomor,kategori,selectKategori,resetFilter">
				@forelse ($data as $v)
					@php
						$hasPdf = $v->dokumen_utama && \Illuminate\Support\Facades\Storage::disk('public')->exists($v->dokumen_utama);
						$desc = \Illuminate\Support\Str::limit(strip_tags($v->abstrak ?: ($v->judul ?? '')), 110);
					@endphp

					<article
						style="animation-delay: {{ $loop->index * 0.06 }}s"
						class="animate-rise group relative flex flex-col overflow-hidden bg-white rounded ring-1 ring-slate-200
							transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-primary/30">

						<span aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

						<div class="flex flex-1 flex-col p-6">
							<div class="flex items-center justify-between gap-2">
								<span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-2.5 py-1 text-[11px] font-semibold text-primary">
									<i class="fa-solid {{ $current['icon'] ?? 'fa-file' }}"></i>
									{{ $current['label'] ?? '' }}
								</span>
								@if ($v->tahun)
									<span class="text-xs text-slate-400 tabular-nums">{{ $v->tahun }}</span>
								@endif
							</div>

							<a wire:navigate.hover href="{{ route('frontend.pembentukan-puu.show', $v->id) }}"
								title="{{ $v->judul }}"
								class="mt-3 text-sm lg:text-base font-semibold text-slate-900 leading-snug line-clamp-2 transition group-hover:text-primary">
								{{ $v->judul }}
							</a>

							<p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-2">{{ $desc }}</p>

							<div class="mt-4 space-y-1.5 text-xs text-slate-500">
								@if ($v->nomor_dokumen)
									<p class="flex items-center gap-1.5"><i class="fa-solid fa-hashtag w-3.5 text-slate-400"></i> {{ $v->nomor_dokumen }}</p>
								@endif
								@if ($v->lembaga_pemrakarsa)
									<p class="flex items-center gap-1.5"><i class="fa-solid fa-building-columns w-3.5 text-slate-400"></i> <span class="line-clamp-1">{{ $v->lembaga_pemrakarsa }}</span></p>
								@endif
							</div>

							<div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
								<a wire:navigate.hover href="{{ route('frontend.pembentukan-puu.show', $v->id) }}"
									class="inline-flex flex-1 items-center justify-center gap-1.5 rounded bg-accent px-3 py-2 text-xs font-semibold text-white transition hover:bg-accent-hover">
									<i class="fa-solid fa-eye"></i> {{ __('Detail') }}
								</a>
								@if ($hasPdf)
									<a href="{{ route('frontend.pembentukan-puu.download', ['id' => $v->id, 'type' => 'dokumen']) }}"
										class="inline-flex items-center justify-center gap-1.5 rounded bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-hover">
										<i class="fa-solid fa-file-arrow-down"></i> {{ __('Unduh') }}
									</a>
								@else
									<span class="inline-flex items-center justify-center gap-1.5 rounded bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-400 cursor-not-allowed" title="{{ __('File tidak tersedia') }}">
										<i class="fa-solid fa-file-circle-xmark"></i>
									</span>
								@endif
							</div>
						</div>
					</article>
				@empty
					<div class="col-span-full">
						<div class="mx-auto max-w-md text-center py-16 px-6 rounded border border-dashed border-gray-200 bg-white">
							<div class="mx-auto flex h-14 w-14 items-center justify-center rounded bg-accent/10 text-accent">
								<i class="fa-solid fa-folder-open text-2xl"></i>
							</div>
							<p class="mt-4 font-semibold text-gray-700">{{ __('Tidak ada data ditemukan.') }}</p>
							@if ($hasFilter)
								<p class="mt-1 text-sm text-gray-400">{{ __('Coba ubah kata kunci atau filter.') }}</p>
							@endif
						</div>
					</div>
				@endforelse
			</div>

			{{-- Pagination --}}
			<div class="mt-10"
				wire:loading.remove wire:target="q,tahun,nomor,kategori,selectKategori,resetFilter">
				{{ $data->links() }}
			</div>
		</div>
	</section>
</div>
