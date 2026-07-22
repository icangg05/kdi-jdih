<!-- MONOGRAFI FEATURE SECTION -->
<section class="relative min-h-dvh flex items-center justify-center text-white overflow-hidden">

	<!-- Background Image -->
	<div
		class="absolute inset-0 bg-cover bg-center"
		style="background-image: url('{{ asset('assets/img/background-2.jpg') }}');">
	</div>

	<!-- Overlay + tint brand -->
	<div class="absolute inset-0 bg-slate-900/85"></div>
	<div class="absolute inset-0 bg-linear-to-b from-black/40 via-transparent to-black/60"></div>

	<!-- ORNAMEN -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute -top-24 -left-24 h-96 w-96 rounded bg-accent/15 blur-3xl"></div>
		<div class="absolute bottom-0 right-0 h-96 w-96 rounded bg-primary/10 blur-3xl"></div>
		<div class="absolute inset-x-0 bottom-0 h-px bg-linear-to-r from-transparent via-primary/40 to-transparent"></div>
	</div>

	<!-- Content -->
	<div class="relative z-10 max-w-6xl w-full px-6 py-20">

		<!-- Title -->
		<div class="animate-rise text-center mb-14">
			<span class="inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-primary backdrop-blur-sm">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('MONOGRAFI HUKUM') }}
			</span>

			<h2 class="mt-5 text-lg md:text-3xl font-bold leading-snug text-balance max-w-4xl mx-auto">
				{{ __('Buku Tanya Jawab Seputar Pembentukan Peraturan Daerah dan Peraturan Kepala Daerah') }}
			</h2>

			<div class="mx-auto mt-6 flex items-center justify-center gap-1">
				<span class="h-1 w-10 rounded bg-primary"></span>
				<span class="h-1 w-1.5 rounded bg-accent"></span>
			</div>
		</div>

		<!-- Info Grid -->
		<div class="animate-rise max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 rounded bg-white/5 ring-1 ring-white/10 backdrop-blur-md p-6 md:p-10" style="animation-delay: .1s">

			<!-- Left Info -->
			<div class="space-y-6">
				<div class="flex items-start gap-3">
					<i class="fas fa-angle-right mt-1 text-primary"></i>
					<div>
						<p class="font-semibold">{{ __('T.E.U. Badan/Pengarang') }}</p>
						<p class="text-sm text-slate-300">
							@forelse ($pengarang as $i => $item)
								{{ $item->name }} {{ $pengarang->count() - 1 !== $i ? '|' : '' }}
							@empty
								-
							@endforelse
						</p>
					</div>
				</div>

				<div class="flex items-start gap-3">
					<i class="fas fa-angle-right mt-1 text-primary"></i>
					<div>
						<p class="font-semibold">{{ __('Subjek') }}</p>
						<ul class="text-sm text-slate-300">
							@forelse ($subjek as $item)
								<li>- {{ $item->subyek }}</li>
							@empty
								<li>-</li>
							@endforelse
						</ul>
					</div>
				</div>

				<div class="flex items-start gap-3">
					<i class="fas fa-angle-right mt-1 text-primary"></i>
					<div>
						<p class="font-semibold">{{ __('Tempat Terbit') }}</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->penerbit ?? '-' }}
						</p>
					</div>
				</div>
			</div>

			<!-- Right Info -->
			<div class="space-y-6">
				<div class="flex items-start gap-3">
					<i class="fas fa-angle-right mt-1 text-primary"></i>
					<div>
						<p class="font-semibold">{{ __('Penerbit') }}</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->tempat_terbit ?? '-' }}
						</p>
					</div>
				</div>

				<div class="flex items-start gap-3">
					<i class="fas fa-angle-right mt-1 text-primary"></i>
					<div>
						<p class="font-semibold">{{ __('Tahun Terbit') }}</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->tahun_terbit ?? '-' }}
						</p>
					</div>
				</div>
			</div>

		</div>

		<!-- Button -->
		<div class="animate-rise mt-14 text-center" style="animation-delay: .2s">
			<a wire:navigate.hover href="{{ route('frontend.dokumen.show', ['monografi', Hashids::encode($monografi->id)]) }}"
				class="inline-flex items-center gap-3 rounded bg-white px-6 py-3 font-semibold text-slate-900 shadow-lg transition hover:bg-slate-100 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent">
				<i class="fas fa-file-lines text-primary"></i>
				{{ __('Lihat Detail') }}
				<i class="fas fa-arrow-right text-xs"></i>
			</a>
		</div>

	</div>
</section>
