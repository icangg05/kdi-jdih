@php
	$contact = config('app.contact');
	$email   = config('app.email');
	$address = config('app.address');

	$fb = config('app.fb');
	$ig = config('app.ig');
	$yt = config('app.yt');
	$tt = config('app.tt');

  $surveiUrl = config('app.surveiUrl');

	$pengunjung = DB::table('pengunjung')->first();
@endphp

<footer class="bg-[#292C36] text-white/60">
	<div class="max-w-7xl mx-auto px-6 py-16">

		<!-- Grid -->
		<div class="grid gap-12 md:grid-cols-2 lg:grid-cols-4">

			<!-- Tentang -->
			<div>
				<div class="flex items-center gap-3 mb-4">
					<img src="{{ asset('assets/img/logo-new-jdih.png') }}" alt="JDIH" class="h-20">
				</div>

				<p class="text-xs lg:text-sm leading-relaxed text-white/50">
					JDIH Kota Kendari hadir untuk meningkatkan pelayanan kepada masyarakat
					atas kebutuhan dokumentasi dan informasi hukum secara lengkap,
					akurat, mudah, dan cepat.
				</p>
			</div>

			<!-- Tautan -->
			<div>
				<h3 class="text-base lg:text-lg font-semibold text-white mb-5 relative">
					Tautan
					<span class="absolute -bottom-2 left-0 w-10 h-1 bg-primary rounded-sm"></span>
				</h3>

				<ul class="space-y-3 text-xs lg:text-sm">
					<li>
						<a target="_blank" href="https://www.kemenkumham.go.id/" class="hover:text-orange-400 transition">
							Portal Kemenkumham RI
						</a>
					</li>
					<li>
						<a target="_blank" href="https://bphn.go.id/" class="hover:text-orange-400 transition">
							Portal BPHN
						</a>
					</li>
					<li>
						<a target="_blank" href="https://jdihn.go.id/" class="hover:text-orange-400 transition">
							Portal JDIHN
						</a>
					</li>
					<li>
						<a target="_blank" href="https://kendarikota.go.id/" class="hover:text-orange-400 transition">
							e-Government Kota Kendari
						</a>
					</li>
				</ul>
			</div>

			<!-- Kontak -->
			<div>
				<h3 class="text-base lg:text-lg font-semibold text-white mb-5 relative">
					Kontak Kami
					<span class="absolute -bottom-2 left-0 w-10 h-1 bg-primary rounded-sm"></span>
				</h3>

				<ul class="space-y-4 text-xs lg:text-sm">
					<li class="flex items-start gap-3">
						<i class="fa-solid fa-location-dot text-primary mt-1"></i>
						<span>Bagian Hukum Setda Kota Kendari, <br>{{ $address }}</span>
					</li>

					<li class="flex items-center gap-3">
						<i class="fa-solid fa-phone text-primary"></i>
						<span>{{ $contact }}</span>
					</li>

					<li class="flex items-center gap-3">
						<i class="fa-solid fa-envelope text-primary"></i>
						<span>{{ $email }}</span>
					</li>
				</ul>
			</div>

			<!-- Statistik -->
			<div>
				<h3 class="text-base lg:text-lg font-semibold text-white mb-5 relative">
					Statistik Pengunjung
					<span class="absolute -bottom-2 left-0 w-10 h-1 bg-primary rounded-sm"></span>
				</h3>

				<div class="space-y-4 text-xs lg:text-sm">
					<div class="flex items-center justify-between">
						<span>Hari Ini</span>
						<span class="px-3 py-1 rounded-full bg-gray-700 text-white">
							{{ number_format(rand(5, 100), 0, ',', '.') }}
						</span>
					</div>

					<div class="flex items-center justify-between">
						<span>Bulan Ini</span>
						<span class="px-3 py-1 rounded-full bg-gray-700 text-white">
							{{ number_format(rand(150, 500), 0, ',', '.') }}
						</span>
					</div>

					<div class="flex items-center justify-between">
						<span>Total</span>
						<span class="px-3 py-1 rounded-full bg-gray-700 text-white">
							{{ number_format($pengunjung->jumlah_keseluruhan, 0, ',', '.') }}
						</span>
					</div>

					<div class="mt-6">
						<p class="text-xs text-slate-400 mb-3">
							Apakah pelayanan dokumentasi hukum dirasa puas?
						</p>

						<!-- Tambahkan link survei -->
<a href="{{ route('survey.create') }}" 
   class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition duration-300" 
   target="_blank">
   📝 Ikuti Survei Kepuasan
</a>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- Bottom -->
	<div class="border-t border-slate-800 bg-[#131313]">
		<div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-4">

			<p class="text-xs lg:text-sm text-white/60">
				© {{ date('Y') }} All Rights Reserved by <a target="_blank" href="https://bphn.go.id/"
					class="text-primary font-semibold">BPHN</a>
			</p>

			<!-- Sosial Media -->
			<!-- Sosial Media -->
			<div class="flex items-center gap-5 text-sm">

				<a
					target="_blank"
					href="{{ $fb['url'] }}"
					class="flex items-center gap-2 transition hover:opacity-80">
					<i class="fa-brands fa-facebook-f text-[#1877F2] text-lg"></i>
					<span class="hidden lg:inline text-white/70">{{ $fb['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $ig['url'] }}"
					class="flex items-center gap-2 transition hover:opacity-80">
					<i class="fa-brands fa-instagram text-[#E4405F] text-lg"></i>
					<span class="hidden lg:inline text-white/70">{{ $ig['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $yt['url'] }}"
					class="flex items-center gap-2 transition hover:opacity-80">
					<i class="fa-brands fa-youtube text-[#FF0000] text-lg"></i>
					<span class="hidden lg:inline text-white/70">{{ $yt['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $tt['url'] }}"
					class="flex items-center gap-2 transition hover:opacity-80">
					<i class="fa-brands fa-tiktok text-white text-lg"></i>
					<span class="hidden lg:inline text-white/70">{{ $tt['label'] }}</span>
				</a>

			</div>


		</div>
	</div>
</footer>
