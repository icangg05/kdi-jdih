<div>
	<x-frontend.breadcrumb
		:title="$title"
		:listNav="[['label' => 'Tentang Kami']]" />

	<!-- Content Section -->
	<section class="bg-linear-to-b from-white via-slate-50 to-slate-100 py-12 lg:py-16">
		<div class="max-w-4xl mx-auto">
			<div class="px-3 ">
				<h2 class="text-lg lg:text-2xl font-semibold text-gray-800 mb-6 border-l-4 border-orange-500 pl-4">
					@if ($title != 'Struktur Organisasi')
						{{ $title == 'Visi' || $title == 'Misi' ? "$title JDIH Pemerintah Kota Kendari" : $title }}
					@else
						{{ $title }}
					@endif
				</h2>
			</div>

			<!-- Rekomendasi -->
			<div class="prose prose-sm prose-slate max-w-none mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 lg:p-8">
				@if ($title == 'Sekilas Sejarah' || $title == 'Dasar Hukum' || $title == 'Visi' || $title == 'Misi')
					{!! $data !!}
				@endif

				@if ($title == 'Struktur Organisasi')
					<div class="text-center">
						<img class="width-87 margin-10px-bottom xs-margin-5px-bottom"
							src="{{ asset('assets/img/struktur-1.png') }}" alt="img">
						<br>
						<br>
						<img class="width-70 margin-10px-bottom margin-0px-left xs-margin-5px-bottom"
							src="{{ asset('assets/img/struktur-2.png') }}" alt="img">
					</div>
				@endif
			</div>
		</div>
	</section>
</div>
