<section class="relative bg-white py-16 lg:py-20 overflow-hidden">

	<!-- ORNAMEN LATAR -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute -top-24 right-0 h-72 w-72 rounded bg-primary/5 blur-3xl"></div>
		<div class="absolute inset-0 opacity-70" style="background-image: radial-gradient(rgba(15,23,42,0.05) 1px, transparent 1px); background-size: 24px 24px; -webkit-mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%); mask-image: radial-gradient(ellipse 75% 60% at 50% 40%, black, transparent 78%);"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-slate-200 to-transparent"></div>
		<i class="fas fa-scale-balanced absolute -bottom-6 -left-4 text-[10rem] text-slate-900/3"></i>
	</div>

	<div class="relative max-w-5xl mx-auto px-6">

		<!-- Header -->
		<div class="animate-rise flex items-end justify-between gap-4 mb-10">
			<div>
				<span class="inline-flex items-center gap-2 rounded border border-slate-200 bg-slate-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">
					<span class="h-1.5 w-1.5 rounded bg-primary"></span>
					{{ __('Produk Hukum') }}
				</span>
				<h2 class="mt-3 text-xl lg:text-2xl font-bold tracking-tight text-slate-900 leading-none">
					{{ __('Peraturan Terbaru') }}
				</h2>
				<div class="mt-3 flex items-center gap-1">
					<span class="h-1 w-8 rounded bg-primary"></span>
					<span class="h-1 w-1 rounded bg-accent"></span>
				</div>
			</div>

			<a wire:navigate.hover href="{{ route('frontend.dokumen.index', 'peraturan') }}"
				class="shrink-0 inline-flex items-center gap-2 rounded bg-primary px-4 py-2 text-xs lg:text-sm font-semibold text-white transition hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-2">
				<i class="fas fa-scale-balanced"></i>
				<span class="text-nowrap">{{ __('Peraturan Lainnya') }}</span>
			</a>
		</div>

		<!-- Cards -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

			@foreach ($peraturan as $v)
				<a wire:navigate.hover href="{{ route('frontend.dokumen.show', ['peraturan', Hashids::encode($v->id)]) }}"
					class="animate-rise group relative overflow-hidden rounded bg-white p-7 lg:p-8 ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-primary/40"
					style="animation-delay: {{ $loop->index * 0.08 }}s">

					<!-- garis aksen atas saat hover -->
					<span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

					<!-- watermark dokumen di sudut -->
					<i class="fas fa-file-contract pointer-events-none absolute -right-3 -top-2 text-6xl text-slate-900/4 transition-colors duration-300 group-hover:text-primary/10" aria-hidden="true"></i>

					<span class="relative inline-block mb-5 rounded border border-primary/40 bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
						{{ $v->jenis_peraturan }} {{ $v->tahun_terbit }}
					</span>

					<h3 class="relative mb-3 text-base lg:text-xl font-bold leading-6 text-slate-900 transition-colors group-hover:text-primary">
						{{ $v->pemrakarsa }}
					</h3>

					<p class="relative line-clamp-3 text-sm leading-relaxed text-slate-600">
						{{ tt($v, 'judul') }}
					</p>

					<span class="relative mt-5 inline-flex items-center gap-1 text-xs font-semibold text-primary transition-all group-hover:gap-2">
						{{ __('Lihat Dokumen') }}
						<i class="fas fa-arrow-right text-[10px]"></i>
					</span>
				</a>
			@endforeach

		</div>
	</div>
</section>
