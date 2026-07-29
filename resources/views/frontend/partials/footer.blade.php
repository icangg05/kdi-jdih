@php
	$contact = config('app.contact');
	$email   = config('app.email');
	$address = config('app.address');

	$fb = config('app.fb');
	$ig = config('app.ig');
	$yt = config('app.yt');
	$tt = config('app.tt');

  $surveiUrl = config('app.surveiUrl');

	$pengunjung = DB::table('pengunjung')->find(1);

	// Angka harian/bulanan hanya sahih bila stempel tanggalnya masih cocok —
	// selama belum ada kunjungan hari ini, kolomnya masih menyimpan angka lama.
	$hariIni  = $pengunjung?->hari === now()->toDateString() ? $pengunjung->jumlah_perhari : 0;
	$bulanIni = $pengunjung?->bulan === now()->format('Y-m') ? $pengunjung->jumlah_bulan : 0;
	$total    = $pengunjung?->jumlah_keseluruhan ?? 0;
@endphp

<footer class="relative overflow-hidden bg-[#292C36] text-white/60">

	<!-- Aksen atas: garis dua-tone brand + shimmer -->
	<div aria-hidden="true" class="relative h-0.5 w-full overflow-hidden">
		<div class="absolute inset-0 bg-linear-to-r from-accent/50 via-primary/70 to-accent/50"></div>
		<div class="absolute inset-y-0 w-1/3 bg-linear-to-r from-transparent via-white/60 to-transparent animate-footer-shimmer"></div>
	</div>

	<!-- Ornamen latar: glow brand + micro-grid titik -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute -top-32 -right-32 h-96 w-96 rounded-full bg-accent/10 blur-3xl"></div>
		<div class="absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>
		<div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 26px 26px;"></div>
		<!-- Watermark tipografi -->
		<p class="absolute -bottom-8 right-0 text-[9rem] lg:text-[13rem] font-extrabold leading-none tracking-tighter text-white/[0.03] select-none whitespace-nowrap">JDIH</p>
	</div>

	<div class="relative max-w-7xl mx-auto px-6 py-10 lg:py-12">

		<!-- Grid -->
		<div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

			<!-- Tentang -->
			<div>
				<div class="flex items-center gap-3 mb-4">
					<img src="{{ asset('assets/img/logo-new-jdih.png') }}" alt="JDIH Kota Kendari" class="h-14">
				</div>

				<p class="text-xs lg:text-sm leading-relaxed text-white/55 max-w-[42ch]">
					{{ __('JDIH Kota Kendari hadir untuk meningkatkan pelayanan kepada masyarakat atas kebutuhan dokumentasi dan informasi hukum secara lengkap, akurat, mudah, dan cepat.') }}
				</p>

				{{-- Aplikasi mobile: belum rilis, jadi kartu statis (bukan tautan) --}}
				<div class="mt-6 flex items-center gap-3 rounded bg-white/5 ring-1 ring-white/10 px-4 py-3 max-w-[42ch]">
					<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-primary/15 text-lg text-primary ring-1 ring-primary/25">
						<i class="fa-brands fa-google-play"></i>
					</span>
					<div class="min-w-0">
						<p class="text-xs text-white/50">{{ __('Aplikasi Mobile') }}</p>
						<p class="text-sm font-semibold text-white">
							{{ __('Segera Hadir') }}
							<span class="ml-1.5 align-middle inline-flex items-center rounded bg-primary/20 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary">
								{{ __('Coming Soon') }}
							</span>
						</p>
					</div>
				</div>
			</div>

			<!-- Tautan -->
			<div>
				<h3 class="text-base font-semibold text-white mb-5 relative">
					{{ __('Tautan') }}
					<span aria-hidden="true" class="absolute -bottom-2 left-0 flex items-center gap-1">
						<span class="w-8 h-1 bg-primary rounded-full"></span>
						<span class="w-1 h-1 bg-accent rounded-full"></span>
					</span>
				</h3>

				<ul class="space-y-2 text-xs lg:text-sm">
					@foreach ([
					    ['Portal Kementerian Hukum RI', 'https://kemenkum.go.id/', 'fa-landmark'],
					    ['Portal BPHN', 'https://bphn.go.id/', 'fa-scale-balanced'],
					    ['Portal JDIHN', 'https://jdihn.go.id/', 'fa-sitemap'],
					    ['e-Government Kota Kendari', 'https://kendarikota.go.id/', 'fa-city'],
					] as [$label, $url, $icon])
						<li>
							<a target="_blank" href="{{ $url }}"
								class="group flex items-center gap-3 rounded px-2 py-1.5 -mx-2 text-white/60 transition-all duration-200 hover:translate-x-1 hover:bg-white/5 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50">
								<span class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-primary/10 text-[11px] text-primary ring-1 ring-primary/20 transition-colors duration-200 group-hover:bg-primary group-hover:text-white group-hover:ring-primary">
									<i class="fa-solid {{ $icon }}"></i>
								</span>
								<span>{{ $label }}</span>
							</a>
						</li>
					@endforeach
				</ul>
			</div>

			<!-- Kontak -->
			<div>
				<h3 class="text-base font-semibold text-white mb-5 relative">
					{{ __('Kontak Kami') }}
					<span aria-hidden="true" class="absolute -bottom-2 left-0 flex items-center gap-1">
						<span class="w-8 h-1 bg-primary rounded-full"></span>
						<span class="w-1 h-1 bg-accent rounded-full"></span>
					</span>
				</h3>

				<ul class="space-y-3 text-xs lg:text-sm">
					<li class="flex items-start gap-3">
						<i class="fa-solid fa-location-dot text-primary mt-1 w-4 text-center"></i>
						<span>{{ __('Bagian Hukum Setda Kota Kendari') }}, <br>{{ $address }}</span>
					</li>

					<li class="flex items-center gap-3">
						<i class="fa-solid fa-phone text-primary w-4 text-center"></i>
						<span>{{ $contact }}</span>
					</li>

					<li class="flex items-center gap-3">
						<i class="fa-solid fa-envelope text-primary w-4 text-center"></i>
						<span>{{ $email }}</span>
					</li>
				</ul>
			</div>

			<!-- Statistik -->
			<div>
				<h3 class="text-base font-semibold text-white mb-5 relative">
					{{ __('Statistik Pengunjung') }}
					<span aria-hidden="true" class="absolute -bottom-2 left-0 flex items-center gap-1">
						<span class="w-8 h-1 bg-primary rounded-full"></span>
						<span class="w-1 h-1 bg-accent rounded-full"></span>
					</span>
				</h3>

				<div class="space-y-3 text-xs lg:text-sm">
					<div class="flex items-center justify-between">
						<span class="text-white/60">{{ __('Hari Ini') }}</span>
						<span class="px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/10 text-white font-medium tabular-nums">
							{{ number_format($hariIni, 0, ',', '.') }}
						</span>
					</div>

					<div class="flex items-center justify-between">
						<span class="text-white/60">{{ __('Bulan Ini') }}</span>
						<span class="px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/10 text-white font-medium tabular-nums">
							{{ number_format($bulanIni, 0, ',', '.') }}
						</span>
					</div>

					<div class="flex items-center justify-between">
						<span class="text-white/60">{{ __('Total') }}</span>
						<span class="px-3 py-1 rounded-full bg-primary/15 ring-1 ring-primary/30 text-primary font-semibold tabular-nums">
							{{ number_format($total, 0, ',', '.') }}
						</span>
					</div>

					<div class="pt-4 mt-1 border-t border-white/10">
						<p class="text-xs text-white/50 mb-3">
							{{ __('Apakah pelayanan dokumentasi hukum dirasa puas?') }}
						</p>

						<!-- Tambahkan link survei -->
						<a href="{{ route('survey.create') }}"
							target="_blank"
							class="group inline-flex items-center gap-3 rounded-full bg-primary pl-5 pr-2 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#292C36]">
							<span>{{ __('Ikuti Survei Kepuasan') }}</span>
							<span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20 transition-transform duration-300 ease-out group-hover:translate-x-0.5">
								<i class="fa-solid fa-arrow-right text-xs"></i>
							</span>
						</a>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- Bottom -->
	<div class="relative border-t border-white/10 bg-[#292C36]">
		<div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">

			<p class="text-xs lg:text-sm text-white/55">
				&copy; {{ date('Y') }} All Rights Reserved by <a target="_blank" href="https://bphn.go.id/"
					class="text-primary font-semibold hover:text-primary-hover transition-colors">BPHN</a>
			</p>

			<!-- Sosial Media -->
			<div class="flex items-center gap-3 lg:gap-5 text-sm">

				<a
					target="_blank"
					href="{{ $fb['url'] }}"
					aria-label="{{ $fb['label'] }}"
					class="group flex items-center gap-2 rounded-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#292C36]">
					<span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 text-white/70 transition-all duration-300 ease-out group-hover:text-white group-hover:-translate-y-0.5 group-hover:bg-[#1877F2] group-hover:ring-[#1877F2]">
						<i class="fa-brands fa-facebook-f"></i>
					</span>
					<span class="hidden lg:inline text-white/70 group-hover:text-white transition-colors">{{ $fb['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $ig['url'] }}"
					aria-label="{{ $ig['label'] }}"
					class="group flex items-center gap-2 rounded-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#292C36]">
					<span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 text-white/70 transition-all duration-300 ease-out group-hover:text-white group-hover:-translate-y-0.5 group-hover:bg-linear-to-tr group-hover:from-[#FEDA75] group-hover:via-[#D62976] group-hover:to-[#4F5BD5] group-hover:ring-[#D62976]">
						<i class="fa-brands fa-instagram"></i>
					</span>
					<span class="hidden lg:inline text-white/70 group-hover:text-white transition-colors">{{ $ig['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $yt['url'] }}"
					aria-label="{{ $yt['label'] }}"
					class="group flex items-center gap-2 rounded-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#292C36]">
					<span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 text-white/70 transition-all duration-300 ease-out group-hover:text-white group-hover:-translate-y-0.5 group-hover:bg-[#FF0000] group-hover:ring-[#FF0000]">
						<i class="fa-brands fa-youtube"></i>
					</span>
					<span class="hidden lg:inline text-white/70 group-hover:text-white transition-colors">{{ $yt['label'] }}</span>
				</a>

				<a
					target="_blank"
					href="{{ $tt['url'] }}"
					aria-label="{{ $tt['label'] }}"
					class="group flex items-center gap-2 rounded-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#292C36]">
					<span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10 text-white/70 transition-all duration-300 ease-out group-hover:text-white group-hover:-translate-y-0.5 group-hover:bg-[#010101] group-hover:ring-[#25F4EE]">
						<i class="fa-brands fa-tiktok"></i>
					</span>
					<span class="hidden lg:inline text-white/70 group-hover:text-white transition-colors">{{ $tt['label'] }}</span>
				</a>

			</div>

		</div>
	</div>
</footer>
