<x-layouts.backend title="Dashboard" :listNav="[['label' => 'Dashboard']]">
  <!-- Statistik Utama -->
  <div class="row">
    <!-- Peraturan -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{ number_format($countPeraturan) }}</h3>
          <p>Peraturan</p>
        </div>
        <div class="icon">
          <i class="ion ion-document-text"></i>
        </div>
        <a class="small-box-footer" href="{{ route('backend.peraturan.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    
    <!-- Monografi -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ number_format($countMonografi) }}</h3>
          <p>Monografi</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-book"></i>
        </div>
        <a class="small-box-footer" href="{{ route('backend.monografi.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    
    <!-- Artikel -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{ number_format($countArtikel) }}</h3>
          <p>Artikel</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-paper"></i>
        </div>
        <a class="small-box-footer" href="{{ route('backend.artikel.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    
    <!-- Putusan -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ number_format($countPutusan) }}</h3>
          <p>Putusan</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-briefcase"></i>
        </div>
        <a class="small-box-footer" href="{{ route('backend.putusan.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>

  <!-- Statistik Tambahan -->
  <div class="row">
    <!-- Pembentukan PUU -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3>{{ number_format($countPembentukan) }}</h3>
          <p>Pembentukan PUU</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-compose"></i>
        </div>
        @php
          $hasPembentukanRoute = Route::has('backend.pembentukan.index');
        @endphp
        @if($hasPembentukanRoute)
        <a class="small-box-footer" href="{{ route('backend.pembentukan.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        @else
        <a class="small-box-footer" href="#">Coming Soon <i class="fa fa-arrow-circle-right"></i></a>
        @endif
      </div>
    </div>
    
    <!-- Disabilitas -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-orange">
        <div class="inner">
          <h3>{{ number_format($countDisabilitas) }}</h3>
          <p>Disabilitas</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-people"></i>
        </div>
        @php
          $hasDisabilitasRoute = Route::has('backend.disabilitas.index');
        @endphp
        @if($hasDisabilitasRoute)
        <a class="small-box-footer" href="{{ route('backend.disabilitas.index') }}">More info <i class="fa fa-arrow-circle-right"></i></a>
        @else
        <a class="small-box-footer" href="#">Coming Soon <i class="fa fa-arrow-circle-right"></i></a>
        @endif
      </div>
    </div>
    
    <!-- Total Dokumen Hukum -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ number_format($totalDokumenHukum) }}</h3>
          <p>Total Dokumen Hukum</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-folder"></i>
        </div>
        <a class="small-box-footer" href="#">Overview <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    
    <!-- Total Koleksi PUU -->
    <div class="col-lg-3 col-xs-6" style="z-index: 0">
      <div class="small-box bg-teal">
        <div class="inner">
          <h3>{{ number_format($totalKoleksiPUU) }}</h3>
          <p>Koleksi PUU</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-box"></i>
        </div>
        <a class="small-box-footer" href="#">Overview <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>

  <!-- Statistik Akses & Download -->
  <div class="row">
    <div class="col-lg-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="ion ion-eye"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Akses Dokumen</span>
          <span class="info-box-number">{{ number_format($totalAkses) }}</span>
          <div class="progress">
            <div class="progress-bar bg-aqua" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            Total views semua dokumen
          </span>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="ion ion-ios-download"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Download</span>
          <span class="info-box-number">{{ number_format($totalDownload) }}</span>
          <div class="progress">
            <div class="progress-bar bg-green" style="width: 100%"></div>
          </div>
          <span class="progress-description">
            Total download semua dokumen
          </span>
        </div>
      </div>
    </div>
  </div>

  

  <!-- Detail Statistik -->
  <div class="row">
    <!-- Statistik Pembentukan PUU -->
    <div class="col-md-6">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Statistik Pembentukan PUU</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="ion ion-ios-list"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Dokumen</span>
                  <span class="info-box-number">{{ number_format($statistikPembentukan['total'] ?? 0) }}</span>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="info-box bg-light-blue">
                <span class="info-box-icon"><i class="ion ion-ios-pulse"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Tahapan</span>
                  <span class="info-box-number">{{ $statistikPembentukan['by_tahapan']->count() ?? 0 }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Table Tahapan -->
          <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Tahapan Pembentukan</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                @foreach($statistikPembentukan['by_tahapan'] ?? [] as $tahapan)
                <tr>
                  <td>{{ $tahapan->tahapan ?? 'Belum Ditentukan' }}</td>
                  <td><span class="badge bg-blue">{{ $tahapan->total ?? 0 }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <!-- Recent Uploads -->
          @if(($statistikPembentukan['recent_uploads'] ?? collect())->count() > 0)
          <h5><i class="fa fa-clock-o"></i> Upload Terbaru</h5>
          <ul class="list-group">
            @foreach($statistikPembentukan['recent_uploads'] as $recent)
            <li class="list-group-item">
              <small class="text-muted pull-right">
                @if(is_string($recent->created_at))
                  {{ date('d/m/Y', strtotime($recent->created_at)) }}
                @elseif($recent->created_at instanceof \Carbon\Carbon)
                  {{ $recent->created_at->format('d/m/Y') }}
                @else
                  {{ $recent->created_at ?? '' }}
                @endif
              </small>
              <p class="list-group-item-text">{{ Str::limit($recent->judul ?? '', 50) }}</p>
            </li>
            @endforeach
          </ul>
          @endif
        </div>
      </div>
    </div>

    <!-- Statistik Disabilitas -->
    <div class="col-md-6">
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Statistik Disabilitas</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="info-box bg-red">
                <span class="info-box-icon"><i class="ion ion-ios-people"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Dokumen</span>
                  <span class="info-box-number">{{ number_format($statistikDisabilitas['total'] ?? 0) }}</span>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="info-box bg-green">
                <span class="info-box-icon"><i class="ion ion-ios-pie"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Jenis Disabilitas</span>
                  <span class="info-box-number">{{ $statistikDisabilitas['by_jenis_disabilitas']->count() ?? 0 }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Table Jenis Disabilitas -->
          <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Jenis Disabilitas</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                @foreach($statistikDisabilitas['by_jenis_disabilitas'] ?? [] as $jenis)
                <tr>
                  <td>{{ $jenis->jenis ?? 'Umum' }}</td>
                  <td><span class="badge bg-green">{{ $jenis->total ?? 0 }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <!-- Recent Uploads -->
          @if(($statistikDisabilitas['recent_uploads'] ?? collect())->count() > 0)
          <h5><i class="fa fa-clock-o"></i> Upload Terbaru</h5>
          <ul class="list-group">
            @foreach($statistikDisabilitas['recent_uploads'] as $recent)
            <li class="list-group-item">
              <small class="text-muted pull-right">
                @if(is_string($recent->created_at))
                  {{ date('d/m/Y', strtotime($recent->created_at)) }}
                @elseif($recent->created_at instanceof \Carbon\Carbon)
                  {{ $recent->created_at->format('d/m/Y') }}
                @else
                  {{ $recent->created_at ?? '' }}
                @endif
              </small>
              <p class="list-group-item-text">{{ Str::limit($recent->judul ?? '', 50) }}</p>
            </li>
            @endforeach
          </ul>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Statistik Survei -->
  @isset($statistikSurvei)
  <div class="row">
    <div class="col-md-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">📊 Statistik Survei Kepuasan Pengguna</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <!-- Total Responden -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Responden</span>
                  <span class="info-box-number">{{ $statistikSurvei['total'] ?? 0 }}</span>
                </div>
              </div>
            </div>
            
            <!-- Rating Overall -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-star"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Rating Rata-rata</span>
                  <span class="info-box-number">{{ $statistikSurvei['average_overall'] ?? 0 }}/5</span>
                </div>
              </div>
            </div>
            
            <!-- Kemudahan Akses -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-search"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Kemudahan Akses</span>
                  <span class="info-box-number">{{ $statistikSurvei['average_kemudahan'] ?? 0 }}/5</span>
                </div>
              </div>
            </div>
            
            <!-- Kecepatan Loading -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-speedometer"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Kecepatan Loading</span>
                  <span class="info-box-number">{{ $statistikSurvei['average_kecepatan'] ?? 0 }}/5</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Distribusi Jenis Pengguna -->
          <div class="row mt-4">
            <div class="col-md-6">
              <h4><i class="fa fa-users"></i> Distribusi Jenis Pengguna</h4>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Jenis Pengguna</th>
                      <th>Jumlah</th>
                      <th>Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($statistikSurvei['jenis_pengguna'] ?? [] as $jenis)
                    @php
                      $percentage = ($statistikSurvei['total'] ?? 0) > 0 ? round(($jenis->total / $statistikSurvei['total']) * 100, 1) : 0;
                    @endphp
                    <tr>
                      <td>{{ $jenis->jenis_pengguna ?? '' }}</td>
                      <td>{{ $jenis->total ?? 0 }}</td>
                      <td>
                        <div class="progress progress-xs">
                          <div class="progress-bar progress-bar-success" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="badge bg-gray">{{ $percentage }}%</span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Rating Distribution -->
            <div class="col-md-6">
              <h4><i class="fa fa-chart-bar"></i> Distribusi Rating</h4>
              <canvas id="surveyChart" height="200"></canvas>
            </div>
          </div>
          
          <!-- Recent Surveys -->
          @if(isset($statistikSurvei['recent_surveys']) && $statistikSurvei['recent_surveys']->count() > 0)
          <div class="row mt-4">
            <div class="col-md-12">
              <h4><i class="fa fa-history"></i> Survei Terbaru</h4>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nama</th>
                      <th>Jenis Pengguna</th>
                      <th>Rating</th>
                      <th>Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($statistikSurvei['recent_surveys'] as $survey)
                    @php
                      // Periksa apakah properti ada sebelum menghitung
                      $kemudahan = isset($survey->kemudahan_akses) ? $survey->kemudahan_akses : 0;
                      $kelengkapan = isset($survey->kelengkapan_informasi) ? $survey->kelengkapan_informasi : 0;
                      $kecepatan = isset($survey->kecepatan_loading) ? $survey->kecepatan_loading : 0;
                      $tampilan = isset($survey->tampilan_antarmuka) ? $survey->tampilan_antarmuka : 0;
                      $relevansi = isset($survey->relevansi_pencarian) ? $survey->relevansi_pencarian : 0;
                      
                      $avg = ($kemudahan + $kelengkapan + $kecepatan + $tampilan + $relevansi) / 5;
                      
                      // Format tanggal
                      $createdAt = $survey->created_at ?? null;
                      if (is_string($createdAt)) {
                          $formattedDate = date('d/m/Y H:i', strtotime($createdAt));
                      } elseif ($createdAt instanceof \Carbon\Carbon) {
                          $formattedDate = $createdAt->format('d/m/Y H:i');
                      } else {
                          $formattedDate = '';
                      }
                    @endphp
                    <tr>
                      <td>{{ $survey->nama ?? '' }}</td>
                      <td><span class="badge bg-blue">{{ $survey->jenis_pengguna ?? '' }}</span></td>
                      <td>
                        <div class="rating">
                          @for($i = 1; $i <= round($avg); $i++)
                            <i class="fa fa-star text-yellow"></i>
                          @endfor
                          <span class="ml-2">{{ round($avg, 1) }}/5</span>
                        </div>
                      </td>
                      <td>{{ $formattedDate }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif
        </div>
        <div class="box-footer">
          @if(Route::has('backend.survey.index'))
          <a href="{{ route('backend.survey.index') }}" class="btn btn-success btn-sm">
            <i class="fa fa-list"></i> Lihat Semua Survei
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endisset

  <!-- Data container untuk chart -->
  <div id="chart-data" 
       data-status-labels='{!! json_encode($statusLabels ?? []) !!}'
       data-status-data='{!! json_encode($statusChartData ?? []) !!}'
       data-status-colors='{!! json_encode($statusColors ?? ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6']) !!}'
       data-jenis-labels='{!! json_encode($jenisLabels ?? []) !!}'
       data-jenis-data='{!! json_encode($jenisChartData ?? []) !!}'
       data-years='{!! json_encode($years ?? []) !!}'
       data-peraturan='{!! json_encode($peraturanData ?? []) !!}'
       data-monografi='{!! json_encode($monografiData ?? []) !!}'
       data-putusan='{!! json_encode($putusanData ?? []) !!}'
       data-pembentukan='{!! json_encode($pembentukanData ?? []) !!}'
       data-disabilitas='{!! json_encode($disabilitasData ?? []) !!}'
       style="display: none;">
  </div>

  <!-- JavaScript untuk Charts -->
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Ambil data dari data attributes
          const dataElement = document.getElementById('chart-data');
          
          // Parse data dengan error handling
          const parseData = (data, defaultValue = []) => {
              try {
                  return data ? JSON.parse(data) : defaultValue;
              } catch (error) {
                  console.error('Error parsing chart data:', error);
                  return defaultValue;
              }
          };
          
          const statusLabels = parseData(dataElement.dataset.statusLabels);
          const statusData = parseData(dataElement.dataset.statusData);
          const statusColors = parseData(dataElement.dataset.statusColors, ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6']);
          
          const jenisLabels = parseData(dataElement.dataset.jenisLabels);
          const jenisData = parseData(dataElement.dataset.jenisData);
          
          const years = parseData(dataElement.dataset.years);
          const peraturanData = parseData(dataElement.dataset.peraturan);
          const monografiData = parseData(dataElement.dataset.monografi);
          const putusanData = parseData(dataElement.dataset.putusan);
          const pembentukanData = parseData(dataElement.dataset.pembentukan);
          const disabilitasData = parseData(dataElement.dataset.disabilitas);

          // Chart Status Keberlakuan
          if (document.getElementById('statusChart')) {
              try {
                  // Validasi data
                  if (statusLabels.length === 0 || statusData.length === 0) {
                      console.warn('No data for status chart');
                      return;
                  }
                  
                  new Chart(document.getElementById('statusChart'), {
                      type: 'doughnut',
                      data: {
                          labels: statusLabels,
                          datasets: [{
                              data: statusData,
                              backgroundColor: statusColors,
                              borderWidth: 1
                          }]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: false,
                          plugins: {
                              legend: {
                                  position: 'bottom'
                              }
                          }
                      }
                  });
              } catch (error) {
                  console.error('Error creating status chart:', error);
              }
          }
          
          // Chart Jenis PUU
          if (document.getElementById('jenisChart')) {
              try {
                  if (jenisLabels.length === 0 || jenisData.length === 0) {
                      console.warn('No data for jenis chart');
                      return;
                  }
                  
                  new Chart(document.getElementById('jenisChart'), {
                      type: 'bar',
                      data: {
                          labels: jenisLabels,
                          datasets: [{
                              label: 'Jumlah Dokumen',
                              data: jenisData,
                              backgroundColor: 'rgba(60, 141, 188, 0.8)',
                              borderColor: 'rgba(60, 141, 188, 1)',
                              borderWidth: 1
                          }]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: false,
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
              } catch (error) {
                  console.error('Error creating jenis chart:', error);
              }
          }
          
          // Timeline Chart
          if (document.getElementById('timelineChart')) {
              try {
                  if (years.length === 0) {
                      console.warn('No data for timeline chart');
                      return;
                  }
                  
                  new Chart(document.getElementById('timelineChart'), {
                      type: 'line',
                      data: {
                          labels: years,
                          datasets: [
                              {
                                  label: 'Peraturan',
                                  data: peraturanData,
                                  borderColor: '#00c0ef',
                                  backgroundColor: 'rgba(0, 192, 239, 0.1)',
                                  tension: 0.3,
                                  fill: true
                              },
                              {
                                  label: 'Monografi',
                                  data: monografiData,
                                  borderColor: '#00a65a',
                                  backgroundColor: 'rgba(0, 166, 90, 0.1)',
                                  tension: 0.3,
                                  fill: true
                              },
                              {
                                  label: 'Putusan',
                                  data: putusanData,
                                  borderColor: '#f56954',
                                  backgroundColor: 'rgba(245, 105, 84, 0.1)',
                                  tension: 0.3,
                                  fill: true
                              },
                              {
                                  label: 'Pembentukan PUU',
                                  data: pembentukanData,
                                  borderColor: '#605ca8',
                                  backgroundColor: 'rgba(96, 92, 168, 0.1)',
                                  tension: 0.3,
                                  fill: true
                              },
                              {
                                  label: 'Disabilitas',
                                  data: disabilitasData,
                                  borderColor: '#ff851b',
                                  backgroundColor: 'rgba(255, 133, 27, 0.1)',
                                  tension: 0.3,
                                  fill: true
                              }
                          ]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: false,
                          scales: {
                              y: {
                                  beginAtZero: true
                              }
                          }
                      }
                  });
              } catch (error) {
                  console.error('Error creating timeline chart:', error);
              }
          }
          
          // Survey Chart (jika ada data)
          @if(isset($statistikSurvei['rating_distribution']))
          if (document.getElementById('surveyChart')) {
              try {
                  // Data survei dari PHP
                  const ratingData = @json($statistikSurvei['rating_distribution'] ?? []);
                  
                  // Validasi data
                  if (!ratingData || Object.keys(ratingData).length === 0) {
                      console.warn('No rating data for survey chart');
                      return;
                  }
                  
                  // Siapkan data untuk chart
                  const labels = ['1 ⭐', '2 ⭐', '3 ⭐', '4 ⭐', '5 ⭐'];
                  const datasets = [
                      {
                          label: 'Kemudahan Akses',
                          data: [
                              ratingData[1]?.kemudahan || 0,
                              ratingData[2]?.kemudahan || 0,
                              ratingData[3]?.kemudahan || 0,
                              ratingData[4]?.kemudahan || 0,
                              ratingData[5]?.kemudahan || 0
                          ],
                          borderColor: '#00c0ef',
                          backgroundColor: 'rgba(0, 192, 239, 0.5)'
                      },
                      {
                          label: 'Kelengkapan Info',
                          data: [
                              ratingData[1]?.kelengkapan || 0,
                              ratingData[2]?.kelengkapan || 0,
                              ratingData[3]?.kelengkapan || 0,
                              ratingData[4]?.kelengkapan || 0,
                              ratingData[5]?.kelengkapan || 0
                          ],
                          borderColor: '#00a65a',
                          backgroundColor: 'rgba(0, 166, 90, 0.5)'
                      }
                  ];
                  
                  new Chart(document.getElementById('surveyChart'), {
                      type: 'bar',
                      data: {
                          labels: labels,
                          datasets: datasets
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: false,
                          scales: {
                              y: {
                                  beginAtZero: true
                              }
                          }
                      }
                  });
              } catch (error) {
                  console.error('Error creating survey chart:', error);
              }
          }
          @endif
      });
  </script>
  @endpush

  <style>
      /* Rating stars styling */
      .rating {
          color: #ffc107;
          display: inline-flex;
          align-items: center;
      }
      
      .rating i {
          margin-right: 2px;
      }
      
      .rating span {
          margin-left: 8px;
          color: #6c757d;
          font-size: 14px;
      }
      
      /* Badge colors */
      .badge.bg-blue {
          background-color: #007bff;
      }
      
      .badge.bg-green {
          background-color: #28a745;
      }
      
      .badge.bg-gray {
          background-color: #6c757d;
      }
      
      /* Small box improvements */
      .small-box .inner h3 {
          font-size: 28px;
          font-weight: bold;
          margin: 0 0 5px 0;
          line-height: 1;
      }
      
      .small-box .inner p {
          font-size: 14px;
      }
      
      /* Progress bar styling */
      .progress-xs {
          height: 8px;
          margin-top: 5px;
      }
      
      /* Chart container */
      .chart {
          position: relative;
          min-height: 200px;
      }
  </style>
</x-layouts.backend>