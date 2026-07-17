<!-- HERO SECTION -->
<section class="relative min-h-screen flex items-center justify-center text-white">

	<!-- Background Image -->
	<div
		class="absolute inset-0 bg-cover bg-center"
		style="background-image: url('{{ asset('assets/img/background-2.jpg') }}');">
	</div>

	<!-- Overlay -->
	<div class="absolute inset-0 bg-slate-900/80"></div>

	<!-- Content -->
	<div class="relative z-10 max-w-6xl w-full px-6">

		<!-- Title -->
		<div class="text-center mb-14">
			<p class="text-primary font-semibold tracking-widest mb-4">
				MONOGRAFI HUKUM
			</p>

			<h1 class="text-lg md:text-3xl font-bold leading-snug">
				Buku Tanya Jawab Seputar Pembentukan Peraturan Daerah dan
				<br class="hidden md:block" />
				Peraturan Kepala Daerah
			</h1>

			<div class="w-14 h-1 bg-primary mx-auto mt-6"></div>
		</div>

		<!-- Info Grid -->
		<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">

			<!-- Left Info -->
			<div class="space-y-6">
				<div class="flex items-start gap-4">
					<span class="text-primary text-xl">➜</span>
					<div>
						<p class="font-semibold">T.E.U. Badan/Pengarang</p>
						<p class="text-sm text-slate-300">
							@forelse ($pengarang as $i => $item)
								{{ $item->name }} {{ $pengarang->count() - 1 !== $i ? '|' : '' }}
							@empty
								-
							@endforelse
						</p>
					</div>
				</div>

				<div class="flex items-start gap-4">
					<span class="text-primary text-xl">➜</span>
					<div>
						<p class="font-semibold">Subjek</p>
						<ul class="text-sm text-slate-300">
							@forelse ($subjek as $item)
								<li>- {{ $item->subyek }}</li>
							@empty
								<li>-</li>
							@endforelse
						</ul>
					</div>
				</div>

				<div class="flex items-start gap-4">
					<span class="text-primary text-xl">➜</span>
					<div>
						<p class="font-semibold">Tempat Terbit</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->penerbit ?? '-' }}
						</p>
					</div>
				</div>
			</div>

			<!-- Right Info -->
			<div class="space-y-6">
				<div class="flex items-start gap-4">
					<span class="text-primary text-xl">➜</span>
					<div>
						<p class="font-semibold">Penerbit</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->tempat_terbit ?? '-' }}
						</p>
					</div>
				</div>

				<div class="flex items-start gap-4">
					<span class="text-primary text-xl">➜</span>
					<div>
						<p class="font-semibold">Tahun Terbit</p>
						<p class="text-sm text-slate-300">
							{{ $monografi->tahun_terbit ?? '-' }}
						</p>
					</div>
				</div>
			</div>

		</div>

		<!-- Button -->
		<div class="mt-14 text-center">
			<a wire:navigate.hover href="{{ route('frontend.dokumen.show', ['monografi', Hashids::encode($monografi->id)]) }}"
				class="inline-flex items-center gap-2 bg-white/50 text-slate-900 px-6 py-3 rounded-md font-semibold transition">
				📄 Lihat Detail →
			</a>
		</div>

	</div>
</section>
