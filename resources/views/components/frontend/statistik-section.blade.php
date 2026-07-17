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
    return $item ?: 'Tidak Terdefinisi';
})->toArray();
$statusChartData = $statusKeberlakuan->pluck('total')->toArray();
$statusColors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1'];

$jenisLabels = $jenisPUU->pluck('jenis_peraturan')->map(function($item) {
    return $item ?: 'Lainnya';
})->toArray();
$jenisChartData = $jenisPUU->pluck('total')->toArray();
@endphp

<section class="relative py-14 lg:py-20 lg:pb-23 overflow-hidden bg-linear-to-b from-[#1F2230] via-[#292C36] to-[#1A1C26]">
	<!-- Background elements -->
	<div class="absolute inset-0 opacity-[0.06]" style="background-image: repeating-linear-gradient(
			-45deg,
			#ffffff 0px,
			#ffffff 1px,
			transparent 1px,
			transparent 12px
		);">
	</div>
	<div class="absolute top-0 right-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl"></div>
	<div class="absolute bottom-0 left-0 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl"></div>

	<div class="relative max-w-7xl mx-auto px-6">
		<!-- Header -->
		<div class="text-center mb-12">
			<h1 class="text-white text-2xl lg:text-3xl font-bold tracking-wide">STATISTIK DOKUMEN HUKUM</h1>
			<p class="text-sm lg:text-base text-gray-300 mt-2">
				Informasi lengkap statistik dokumen hukum dan peraturan perundang-undangan.
			</p>
			<div class="w-12 h-1 bg-orange-500 mx-auto mt-4 rounded"></div>
		</div>

		<!-- Statistik Utama -->
		<div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-white mb-12">
			<!-- Total Dokumen Hukum -->
			<div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 transition hover:scale-[1.02] hover:border-orange-500/30">
				<div class="flex items-center gap-4">
					<div class="bg-blue-500/20 p-3 rounded-lg">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/peraturan.svg') }}" alt="Total Dokumen">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold">{{ number_format($totalDokumenHukum, 0, ',', '.') }}</p>
						<p class="text-blue-300 text-[11px] lg:text-sm font-semibold">DOKUMEN HUKUM</p>
						<p class="text-gray-400 text-xs mt-1">Total semua dokumen</p>
					</div>
				</div>
			</div>

			<!-- Koleksi PUU -->
			<div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 transition hover:scale-[1.02] hover:border-orange-500/30">
				<div class="flex items-center gap-4">
					<div class="bg-green-500/20 p-3 rounded-lg">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/monogrofi.svg') }}" alt="Koleksi PUU">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold">{{ number_format($totalKoleksiPUU, 0, ',', '.') }}</p>
						<p class="text-green-300 text-[11px] lg:text-sm font-semibold">KOLEKSI PUU</p>
						<p class="text-gray-400 text-xs mt-1">Peraturan Perundang-undangan</p>
					</div>
				</div>
			</div>

			<!-- Total Akses -->
			<div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 transition hover:scale-[1.02] hover:border-orange-500/30">
				<div class="flex items-center gap-4">
					<div class="bg-purple-500/20 p-3 rounded-lg">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/artikel.svg') }}" alt="Total Akses">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold">{{ number_format($totalAkses, 0, ',', '.') }}</p>
						<p class="text-purple-300 text-[11px] lg:text-sm font-semibold">TOTAL AKSES</p>
						<p class="text-gray-400 text-xs mt-1">Dokumen diakses</p>
					</div>
				</div>
			</div>

			<!-- Total Download -->
			<div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 transition hover:scale-[1.02] hover:border-orange-500/30">
				<div class="flex items-center gap-4">
					<div class="bg-orange-500/20 p-3 rounded-lg">
						<img class="w-10 lg:w-14" src="{{ asset('assets/img/yurisprudensi.svg') }}" alt="Total Download">
					</div>
					<div>
						<p class="text-2xl lg:text-3xl font-bold">{{ number_format($totalDownload, 0, ',', '.') }}</p>
						<p class="text-orange-300 text-[11px] lg:text-sm font-semibold">TOTAL DOWNLOAD</p>
						<p class="text-gray-400 text-xs mt-1">Dokumen diunduh</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Grid untuk Statistik Detail -->
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto mb-12">
			
			<!-- Chart Status Keberlakuan -->
			<div class="bg-white rounded-2xl p-6 shadow-lg">
				<h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
					<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
					</svg>
					STATUS KEBERLAKUAN PUU
				</h2>
				<p class="text-xs text-gray-500 mb-4">Distribusi berdasarkan status hukum</p>
				<div class="relative h-72">
					<canvas id="statusChart"></canvas>
				</div>
				<div class="mt-4 grid grid-cols-2 gap-2">
					@foreach($statusKeberlakuan as $status)
					<div class="flex items-center justify-between p-2 bg-gray-50 rounded">
						<span class="text-sm">{{ $status->status ?? 'Tidak Terdefinisi' }}</span>
						<span class="font-bold text-blue-600">{{ $status->total }}</span>
					</div>
					@endforeach
				</div>
			</div>

			<!-- Chart Jenis PUU -->
			<div class="bg-white rounded-2xl p-6 shadow-lg">
				<h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
					<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
					</svg>
					JENIS PERATURAN PERUNDANG-UNDANGAN
				</h2>
				<p class="text-xs text-gray-500 mb-4">Klasifikasi berdasarkan jenis dokumen</p>
				<div class="relative h-72">
					<canvas id="jenisChart"></canvas>
				</div>
				<div class="mt-4 space-y-2 max-h-40 overflow-y-auto">
					@foreach($jenisPUU as $jenis)
					<div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
						<span class="text-sm">{{ $jenis->jenis_peraturan ?? 'Lainnya' }}</span>
						<span class="font-bold text-green-600">{{ $jenis->total }}</span>
					</div>
					@endforeach
				</div>
			</div>

		</div>

		<!-- Grafik Timeline -->
		<div class="mt-10 bg-white rounded-2xl p-8 shadow-lg max-w-5xl mx-auto">
			<h2 class="text-xl font-bold text-gray-800 text-center">
				PERKEMBANGAN DOKUMEN HUKUM 5 TAHUN TERAKHIR
			</h2>
			<p class="text-xs lg:text-sm text-gray-500 text-center mt-1">
				Jumlah berkas berdasarkan jenis dokumen ({{ $years[0] }} - {{ end($years) }})
			</p>
			<div class="w-10 h-1 bg-orange-500 mx-auto my-4 rounded"></div>
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
						label: 'Peraturan',
						data: @json($peraturanData),
						backgroundColor: 'rgba(59, 130, 246, 0.7)',
						borderRadius: 6
					},
					{
						label: 'Monografi',
						data: @json($monografiData),
						backgroundColor: 'rgba(239, 68, 68, 0.7)',
						borderRadius: 6
					},
					{
						label: 'Putusan',
						data: @json($putusanData),
						backgroundColor: 'rgba(139, 92, 246, 0.7)',
						borderRadius: 6
					},
					{
						label: 'Pembentukan PUU',
						data: @json($pembentukanData),
						backgroundColor: 'rgba(245, 158, 11, 0.7)',
						borderRadius: 6
					},
					{
						label: 'Disabilitas',
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
					label: 'Jumlah Dokumen',
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