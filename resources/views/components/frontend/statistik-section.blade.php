@php
// SEMUA QUERY DIPINDAHKAN KE ATAS FILE
// Data untuk timeline chart dari semua tabel
$currentYear = date('Y');
$startYear = $currentYear - 4;
$years = range($startYear, $currentYear);

// Fungsi helper dengan error handling
function safeSum($table, $column, $default = 0) {
    try {
        return DB::table($table)->sum($column) ?? $default;
    } catch (\Exception $e) {
        return $default;
    }
}

function safeCount($table, $conditions = []) {
    try {
        $query = DB::table($table);
        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }
        return $query->count();
    } catch (\Exception $e) {
        return 0;
    }
}

function safeSelect($table, $columns, $groupBy = null, $where = []) {
    try {
        $query = DB::table($table)->select($columns);
        
        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }
        
        if ($groupBy) {
            $query->groupBy($groupBy);
        }
        
        return $query->get();
    } catch (\Exception $e) {
        return collect([]);
    }
}

// 1. Hitung statistik dasar dari document dengan error handling
$countPeraturan = safeCount('document', ['tipe_dokumen' => 1]);
$countMonografi = safeCount('document', ['tipe_dokumen' => 2]);
$countArtikel = safeCount('document', ['tipe_dokumen' => 3]);
$countPutusan = safeCount('document', ['tipe_dokumen' => 4]);

// 2. Hitung dari tabel lain dengan error handling
$countPembentukan = safeCount('pembentukan_puu');
$countDisabilitas = safeCount('disabilitas');

// 3. Total semua dokumen hukum
$totalDokumenHukum = $countPeraturan + $countMonografi + $countArtikel + $countPutusan 
                    + $countPembentukan + $countDisabilitas;

// 4. Total koleksi PUU (Peraturan + Pembentukan PUU)
$totalKoleksiPUU = $countPeraturan + $countPembentukan;

// 5. Statistik status keberlakuan
// Dari tabel document - kolom 'status'
$statusFromDocument = safeSelect('document', 
    [DB::raw('status as status'), DB::raw('COUNT(*) as total')],
    'status',
    ['tipe_dokumen' => 1]
);

// Dari tabel pembentukan_puu - kolom 'status_dokumen'
$statusFromPembentukan = safeSelect('pembentukan_puu',
    [DB::raw('status_dokumen as status'), DB::raw('COUNT(*) as total')],
    'status_dokumen'
);

// Dari tabel disabilitas - kolom 'status_dokumen'
$statusFromDisabilitas = safeSelect('disabilitas',
    [DB::raw('status_dokumen as status'), DB::raw('COUNT(*) as total')],
    'status_dokumen'
);

$statusKeberlakuan = $statusFromDocument
    ->concat($statusFromPembentukan)
    ->concat($statusFromDisabilitas);

// 6. Statistik jenis PUU
// Dari tabel document - kolom 'jenis_peraturan'
$jenisFromDocument = safeSelect('document',
    [DB::raw('jenis_peraturan as jenis_peraturan'), DB::raw('COUNT(*) as total')],
    'jenis_peraturan',
    ['tipe_dokumen' => 1]
);

// Dari tabel pembentukan_puu - kolom 'jenis_dokumen'
$jenisFromPembentukan = safeSelect('pembentukan_puu',
    [DB::raw('jenis_dokumen as jenis_peraturan'), DB::raw('COUNT(*) as total')],
    'jenis_dokumen'
);

// Dari tabel disabilitas - kolom 'jenis_dokumen'
$jenisFromDisabilitas = safeSelect('disabilitas',
    [DB::raw('jenis_dokumen as jenis_peraturan'), DB::raw('COUNT(*) as total')],
    'jenis_dokumen'
);

$jenisPUU = $jenisFromDocument
    ->concat($jenisFromPembentukan)
    ->concat($jenisFromDisabilitas);

// 7. Statistik akses/download dengan error handling
$totalAkses = safeSum('document', 'hit_see')
            + safeSum('pembentukan_puu', 'views')
            + safeSum('disabilitas', 'views');

$totalDownload = safeSum('document', 'hit_download')
               + safeSum('pembentukan_puu', 'jumlah_download')
               + safeSum('disabilitas', 'jumlah_download');

// 8. Data untuk chart timeline dengan error handling
$peraturanData = [];
$monografiData = [];
$putusanData = [];
$pembentukanData = [];
$disabilitasData = [];

foreach ($years as $year) {
    // Dari document
    $peraturanData[] = DB::table('document')
        ->where('tipe_dokumen', 1)
        ->whereYear('created_at', $year)
        ->count();
    
    $monografiData[] = DB::table('document')
        ->where('tipe_dokumen', 2)
        ->whereYear('created_at', $year)
        ->count();
    
    $putusanData[] = DB::table('document')
        ->where('tipe_dokumen', 4)
        ->whereYear('created_at', $year)
        ->count();
    
    // Dari pembentukan_puu
    try {
        $pembentukanData[] = DB::table('pembentukan_puu')
            ->whereYear('created_at', $year)
            ->count();
    } catch (\Exception $e) {
        $pembentukanData[] = 0;
    }
    
    // Dari disabilitas
    try {
        $disabilitasData[] = DB::table('disabilitas')
            ->whereYear('created_at', $year)
            ->count();
    } catch (\Exception $e) {
        $disabilitasData[] = 0;
    }
}

// 9. Siapkan data untuk chart
$statusLabels = $statusKeberlakuan->pluck('status')->map(function($item) {
    return $item ?: __('Tidak Terdefinisi');
})->toArray();
$statusChartData = $statusKeberlakuan->pluck('total')->toArray();
$statusColors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1'];

$jenisLabels = $jenisPUU->pluck('jenis_peraturan')->map(function($item) {
    return $item ?: __('Lainnya');
})->toArray();
$jenisChartData = $jenisPUU->pluck('total')->toArray();
@endphp

<section class="relative py-16 lg:py-24 overflow-hidden bg-linear-to-b from-[#1F2230] via-[#292C36] to-[#1A1C26]">

	<!-- ORNAMEN LATAR: stripe halus + glow brand + garis atas -->
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0 opacity-[0.05]" style="background-image: repeating-linear-gradient(-45deg, #ffffff 0px, #ffffff 1px, transparent 1px, transparent 12px);"></div>
		<div class="absolute top-0 right-0 h-72 w-72 rounded bg-accent/10 blur-3xl"></div>
		<div class="absolute bottom-0 left-0 h-72 w-72 rounded bg-primary/10 blur-3xl"></div>
		<div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/15 to-transparent"></div>
	</div>

	<div class="relative max-w-7xl mx-auto px-6">

		<!-- HEADER -->
		<div class="animate-rise mx-auto mb-12 max-w-2xl text-center">
			<span class="inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300 backdrop-blur-sm">
				<span class="h-1.5 w-1.5 rounded bg-primary"></span>
				{{ __('Data & Statistik') }}
			</span>
			<h2 class="mt-4 text-2xl lg:text-3xl font-bold tracking-tight text-white">{{ __('STATISTIK DOKUMEN HUKUM') }}</h2>
			<p class="mt-2 text-sm lg:text-base text-slate-300">
				{{ __('Informasi lengkap statistik dokumen hukum dan peraturan perundang-undangan.') }}
			</p>
			<div class="mx-auto mt-4 flex items-center justify-center gap-1">
				<span class="h-1 w-8 rounded bg-primary"></span>
				<span class="h-1 w-1 rounded bg-accent"></span>
			</div>
		</div>

		<!-- STATISTIK UTAMA -->
		<div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6 text-white mb-12">

			<!-- Total Dokumen Hukum -->
			<div class="animate-rise group rounded bg-white/5 p-5 lg:p-6 ring-1 ring-white/10 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:ring-primary/30" style="animation-delay: 0s">
				<div class="flex items-center gap-4">
					<div class="shrink-0 rounded bg-accent/15 p-3 ring-1 ring-accent/25">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/peraturan.svg') }}" alt="Total Dokumen">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold tabular-nums">{{ number_format($totalDokumenHukum, 0, ',', '.') }}</p>
						<p class="text-accent text-[11px] lg:text-sm font-semibold">{{ __('DOKUMEN HUKUM') }}</p>
						<p class="text-slate-400 text-xs mt-1">{{ __('Total semua dokumen') }}</p>
					</div>
				</div>
			</div>

			<!-- Koleksi PUU -->
			<div class="animate-rise group rounded bg-white/5 p-5 lg:p-6 ring-1 ring-white/10 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:ring-primary/30" style="animation-delay: .05s">
				<div class="flex items-center gap-4">
					<div class="shrink-0 rounded bg-primary/15 p-3 ring-1 ring-primary/25">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/monogrofi.svg') }}" alt="Koleksi PUU">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold tabular-nums">{{ number_format($totalKoleksiPUU, 0, ',', '.') }}</p>
						<p class="text-primary text-[11px] lg:text-sm font-semibold">{{ __('KOLEKSI PUU') }}</p>
						<p class="text-slate-400 text-xs mt-1">{{ __('Peraturan Perundang-undangan') }}</p>
					</div>
				</div>
			</div>

			<!-- Total Akses -->
			<div class="animate-rise group rounded bg-white/5 p-5 lg:p-6 ring-1 ring-white/10 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:ring-primary/30" style="animation-delay: .1s">
				<div class="flex items-center gap-4">
					<div class="shrink-0 rounded bg-accent/15 p-3 ring-1 ring-accent/25">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/artikel.svg') }}" alt="Total Akses">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold tabular-nums">{{ number_format($totalAkses, 0, ',', '.') }}</p>
						<p class="text-accent text-[11px] lg:text-sm font-semibold">{{ __('TOTAL AKSES') }}</p>
						<p class="text-slate-400 text-xs mt-1">{{ __('Dokumen diakses') }}</p>
					</div>
				</div>
			</div>

			<!-- Total Download -->
			<div class="animate-rise group rounded bg-white/5 p-5 lg:p-6 ring-1 ring-white/10 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:ring-primary/30" style="animation-delay: .15s">
				<div class="flex items-center gap-4">
					<div class="shrink-0 rounded bg-primary/15 p-3 ring-1 ring-primary/25">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/yurisprudensi.svg') }}" alt="Total Download">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold tabular-nums">{{ number_format($totalDownload, 0, ',', '.') }}</p>
						<p class="text-primary text-[11px] lg:text-sm font-semibold">{{ __('TOTAL DOWNLOAD') }}</p>
						<p class="text-slate-400 text-xs mt-1">{{ __('Dokumen diunduh') }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- GRID STATISTIK DETAIL -->
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 max-w-6xl mx-auto mb-12">

			<!-- Chart Status Keberlakuan -->
			<div class="animate-rise rounded bg-white p-6 shadow-lg ring-1 ring-black/5">
				<h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
					</svg>
					{{ __('STATUS KEBERLAKUAN PUU') }}
				</h3>
				<p class="text-xs text-gray-500 mb-4">{{ __('Distribusi berdasarkan status hukum') }}</p>
				<div class="relative h-72">
					<canvas id="statusChart"></canvas>
				</div>
				<div class="mt-4 grid grid-cols-2 gap-2">
					@foreach($statusKeberlakuan as $status)
					<div class="flex items-center justify-between p-2 bg-gray-50 rounded">
						<span class="text-sm text-gray-700">{{ Str::title($status->status ?? __('Tidak Terdefinisi')) }}</span>
						<span class="font-bold text-accent tabular-nums">{{ $status->total }}</span>
					</div>
					@endforeach
				</div>
			</div>

			<!-- Chart Jenis PUU -->
			<div class="animate-rise rounded bg-white p-6 shadow-lg ring-1 ring-black/5">
				<h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
					<svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
					</svg>
					{{ __('JENIS PERATURAN PERUNDANG-UNDANGAN') }}
				</h3>
				<p class="text-xs text-gray-500 mb-4">{{ __('Klasifikasi berdasarkan jenis dokumen') }}</p>
				<div class="relative h-72">
					<canvas id="jenisChart"></canvas>
				</div>
				<div class="mt-4 space-y-2 max-h-40 overflow-y-auto">
					@foreach($jenisPUU as $jenis)
					<div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
						<span class="text-sm text-gray-700">{{ Str::upper($jenis->jenis_peraturan ?? __('Lainnya')) }}</span>
						<span class="font-bold text-primary tabular-nums">{{ $jenis->total }}</span>
					</div>
					@endforeach
				</div>
			</div>

		</div>

		<!-- GRAFIK TIMELINE -->
		<div class="animate-rise mt-10 rounded bg-white p-6 lg:p-8 shadow-lg ring-1 ring-black/5 max-w-5xl mx-auto">
			<h3 class="text-xl font-bold text-gray-800 text-center">
				{{ __('PERKEMBANGAN DOKUMEN HUKUM 5 TAHUN TERAKHIR') }}
			</h3>
			<p class="text-xs lg:text-sm text-gray-500 text-center mt-1">
				{{ __('Jumlah berkas berdasarkan jenis dokumen') }} ({{ $years[0] }} - {{ end($years) }})
			</p>
			<div class="mx-auto my-4 flex items-center justify-center gap-1">
				<span class="h-1 w-8 rounded bg-primary"></span>
				<span class="h-1 w-1 rounded bg-accent"></span>
			</div>
			<div class="relative h-80">
				<canvas id="timelineChart"></canvas>
			</div>
		</div>

	</div>
</section>

<!-- Chart Script -->
<script data-navigate-once>
	document.addEventListener('livewire:navigated', () => {
		
		// Fungsi untuk membuat chart
		function createChart(id, type, config) {
			const el = document.getElementById(id);
			if (!el) return null;
			
			if (el._chartInstance) {
				el._chartInstance.destroy();
			}
			
			const ctx = el.getContext('2d');
			const chart = new Chart(ctx, config);
			el._chartInstance = chart;
			return chart;
		}

		// Chart Timeline
		createChart('timelineChart', 'bar', {
			type: 'bar',
			data: {
				labels: @json($years),
				datasets: [
					{
						label: @json(__('Peraturan')),
						data: @json($peraturanData),
						backgroundColor: 'rgba(59, 130, 246, 0.7)',
						borderRadius: 6
					},
					{
						label: @json(__('Monografi')),
						data: @json($monografiData),
						backgroundColor: 'rgba(239, 68, 68, 0.7)',
						borderRadius: 6
					},
					{
						label: @json(__('Putusan')),
						data: @json($putusanData),
						backgroundColor: 'rgba(139, 92, 246, 0.7)',
						borderRadius: 6
					},
					{
						label: @json(__('Pembentukan PUU')),
						data: @json($pembentukanData),
						backgroundColor: 'rgba(245, 158, 11, 0.7)',
						borderRadius: 6
					},
					{
						label: @json(__('Disabilitas')),
						data: @json($disabilitasData),
						backgroundColor: 'rgba(16, 185, 129, 0.7)',
						borderRadius: 6
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'top',
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});

		// Chart Status Keberlakuan
		createChart('statusChart', 'doughnut', {
			type: 'doughnut',
			data: {
				labels: @json($statusLabels),
				datasets: [{
					data: @json($statusChartData),
					backgroundColor: @json($statusColors),
					borderWidth: 2,
					borderColor: '#ffffff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'right',
						labels: {
							padding: 15,
							boxWidth: 12,
							font: {
								size: 11
							}
						}
					}
				},
				cutout: '65%'
			}
		});

		// Chart Jenis PUU
		createChart('jenisChart', 'bar', {
			type: 'bar',
			data: {
				labels: @json($jenisLabels),
				datasets: [{
					label: @json(__('Jumlah Dokumen')),
					data: @json($jenisChartData),
					backgroundColor: 'rgba(16, 185, 129, 0.7)',
					borderColor: 'rgba(16, 185, 129, 1)',
					borderWidth: 1,
					borderRadius: 6
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: {
					legend: {
						display: false
					}
				},
				scales: {
					x: {
						beginAtZero: true
					},
					y: {
						ticks: {
							font: {
								size: 11
							}
						}
					}
				}
			}
		});

	});
</script>