<section class="bg-white py-16">
	<div class="max-w-5xl mx-auto px-6">

		<!-- Header -->
		<div class="flex items-center justify-between mb-10">
			<div>
				<h2 class="text-xl lg:text-2xl font-bold text-gray-900 leading-none">
					Peraturan Terbaru
				</h2>
				<div class="w-10 h-1 bg-primary mt-2 rounded"></div>
			</div>

			<a wire:navigate.hover href="{{ route('frontend.dokumen.index', 'peraturan') }}"
				class="text-nowrap text-xs lg:text-sm flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-md font-semibold hover:bg-primary-hover transition">
				⚖️ Peraturan Lainnya
			</a>
		</div>

		<!-- Cards -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

			@foreach ($peraturan as $v)
				<a wire:navigate.hover href="{{ route('frontend.dokumen.show', ['peraturan', Hashids::encode($v->id)]) }}"
					class="group border border-gray-300 rounded-3xl p-7 lg:p-8 transition duration-300 hover:bg-primary hover:border-primary">

					<span
						class="inline-block mb-5 px-4 py-1 text-xs font-semibold rounded-full
					border border-primary text-primary
					group-hover:bg-white">
						{{ $v->jenis_peraturan }} {{ $v->tahun_terbit }}
					</span>

					<h3 class="leading-6 text-base lg:text-xl font-bold text-gray-900 mb-4 group-hover:text-white">
						{{ $v->pemrakarsa }}
					</h3>

					<p class="line-clamp-3 text-sm text-gray-600 leading-relaxed group-hover:text-white">
						{{ $v->judul }}
					</p>
				</a>
			@endforeach

		</div>
	</div>
</section>
