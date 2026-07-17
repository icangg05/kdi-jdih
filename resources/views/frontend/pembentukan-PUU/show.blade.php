<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->judul }} - JDIH Kota Kendari</title>
    <meta name="description" content="{{ Str::limit($document->abstrak ?? $document->judul, 160) }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #8b5cf6;
            --accent: #a855f7;
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        
        .bg-gradient-purple-blue {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 25%, #8b5cf6 50%, #a855f7 75%, #d946ef 100%);
        }
        
        .text-gradient {
            background: linear-gradient(90deg, #4f46e5, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.1);
        }
        
        /* Styling untuk konten */
        .content-wrapper {
            max-width: 1200px;
        }
        
        .info-card {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
            border: 1px solid rgba(79, 70, 229, 0.1);
            border-radius: 12px;
        }
        
        /* CSS untuk jarak tambahan */
        .dokumen-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
        }
        
        .sidebar-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
        }
        
        .empty-field {
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body class="bg-white text-gray-800">
    <!-- Include Header -->
    @include('frontend.partials.header')
    
    <!-- Breadcrumb -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-indigo-100">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('frontend.beranda') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-home mr-2"></i>
                            Beranda
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <a href="{{ route('frontend.pembentukan-puu.index') }}" class="ml-3 text-sm font-medium text-gray-700 hover:text-indigo-600">
                                Pembentukan PUU
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center gap-4 mb-3"> <!-- DIPERBARUI: gap-4 untuk jarak -->
    <span class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-medium mt-1"> <!-- DITAMBAHKAN: mt-1 -->
        {{ $kategoriLabel }}
    </span>
    <span class="text-gray-500 text-sm">
        <i class="far fa-calendar-alt mr-1"></i>
        {{ $document->tahun }}
    </span>
</div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                            <span class="ml-3 text-sm font-medium text-indigo-600 truncate max-w-xs md:max-w-md">
                                {{ Str::limit($document->judul, 40) }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Judul Dokumen -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            @php
                                $jenisLabels = [
                                    'naskah_akademik' => 'Naskah Akademik',
                                    'rancangan_puu' => 'Rancangan PUU',
                                    'penelitian_hukum' => 'Penelitian Hukum',
                                    'pengkajian_hukum' => 'Pengkajian Hukum',
                                    'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
                                    'analisis_evaluasi' => 'Analisis Evaluasi',
                                ];
                                $jenisLabel = $jenisLabels[$document->jenis_dokumen] ?? $document->jenis_dokumen;
                            @endphp
                            <span class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $jenisLabel }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $document->tahun }}
                            </span>
                        </div>
                        
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                            {{ $document->judul }}
                        </h1>
                        
                        <!-- Stats -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6">
                            <span class="flex items-center">
                                <i class="far fa-eye mr-2 text-indigo-500"></i>
                                {{ $document->views }} dilihat
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-download mr-2 text-green-500"></i>
                                {{ $document->jumlah_download }} download
                            </span>
                            <span class="flex items-center">
                                <i class="far fa-clock mr-2 text-purple-500"></i>
                                {{ \Carbon\Carbon::parse($document->tanggal_unggah)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Dokumen -->
                    <div class="info-card p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                            Informasi Dokumen
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Jenis Dokumen</h4>
                                <p class="text-gray-900">{{ $jenisLabel }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Nomor Dokumen</h4>
                                <p class="text-gray-900">{{ $document->nomor_dokumen ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Lembaga Pemrakarsa</h4>
                                <p class="text-gray-900">{{ $document->lembaga_pemrakarsa }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Status Dokumen</h4>
                                <p class="text-gray-900">{{ $document->status_dokumen }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Tahapan Pembentukan</h4>
                                <p class="text-gray-900">
                                    @php
                                        $tahapanLabels = [
                                            'pra_legislasi' => 'Pra-Legislasi',
                                            'penyusunan_ruu' => 'Penyusunan RUU',
                                            'pembahasan_dpr' => 'Pembahasan DPR',
                                            'pengundangan' => 'Pengundangan',
                                            'evaluasi' => 'Evaluasi',
                                        ];
                                        $tahapanLabel = $tahapanLabels[$document->tahapan_pembentukan] ?? $document->tahapan_pembentukan;
                                    @endphp
                                    {{ $tahapanLabel ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Penulis/Penyusun</h4>
                                <p class="text-gray-900">{{ $document->penulis ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Editor/Reviewer</h4>
                                <p class="text-gray-900">{{ $document->editor ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Pengunggah</h4>
                                <p class="text-gray-900">{{ $document->pengunggah }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 mb-1">Tanggal Unggah</h4>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($document->tanggal_unggah)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Abstrak/Ringkasan -->
                    @if($document->abstrak)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-indigo-500"></i>
                            Abstrak/Ringkasan
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $document->abstrak }}
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Kata Kunci -->
                    @if($document->kata_kunci)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-tags mr-2 text-indigo-500"></i>
                            Kata Kunci
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $keywords = array_filter(array_map('trim', explode(',', $document->kata_kunci)));
                            @endphp
                            @if(count($keywords) > 0)
                                @foreach($keywords as $keyword)
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                                        {{ $keyword }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-500 italic">Tidak ada kata kunci</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Informasi Tambahan Berdasarkan Jenis Dokumen -->
                    <div class="mb-8 dokumen-section">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-clipboard-list mr-2 text-indigo-500"></i>
                            Informasi Spesifik {{ $jenisLabel }}
                        </h3>
                        
                        @php
                            // Cek apakah ada data spesifik untuk jenis dokumen ini
                            $hasSpecificData = false;
                            switch($document->jenis_dokumen) {
                                case 'naskah_akademik':
                                    $fields = ['rumusan_masalah', 'tujuan_penelitian', 'metodologi_penelitian', 'tim_penyusun', 'tanggal_penyelesaian'];
                                    break;
                                case 'penelitian_hukum':
                                    $fields = ['latar_belakang', 'fokus_penelitian', 'hasil_penelitian', 'rekomendasi', 'lokasi_penelitian'];
                                    break;
                                case 'rancangan_puu':
                                    $fields = ['jenis_rancangan', 'prolegnas', 'inisiator', 'pansus_panja', 'tanggal_pengajuan'];
                                    break;
                                case 'pengkajian_hukum':
                                    $fields = ['objek_pengkajian', 'jenis_pengkajian', 'tujuan_pengkajian', 'kesimpulan_pengkajian', 'tanggal_pengkajian'];
                                    break;
                                case 'pengkajian_konstitusi':
                                    $fields = ['aspek_konstitusi', 'jenis_pengkajian_konstitusi', 'dasar_hukum_pengkajian', 'instansi_pengkaji', 'implikasi_konstitusional'];
                                    break;
                                case 'analisis_evaluasi':
                                    $fields = ['objek_evaluasi', 'metode_evaluasi', 'indikator_evaluasi', 'temuan_evaluasi', 'rekomendasi_perbaikan', 'periode_evaluasi'];
                                    break;
                                default:
                                    $fields = [];
                            }
                            
                            // Cek jika ada minimal satu field yang memiliki nilai
                            foreach($fields as $field) {
                                if(!empty($document->$field)) {
                                    $hasSpecificData = true;
                                    break;
                                }
                            }
                        @endphp
                        
                        @if($hasSpecificData)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @switch($document->jenis_dokumen)
                                    @case('naskah_akademik')
                                        @include('frontend.pembentukan-puu.partials.fields-naskah-akademik', ['document' => $document])
                                        @break
                                        
                                    @case('penelitian_hukum')
                                        @include('frontend.pembentukan-puu.partials.fields-penelitian-hukum', ['document' => $document])
                                        @break
                                        
                                    @case('rancangan_puu')
                                        @include('frontend.pembentukan-puu.partials.fields-rancangan-puu', ['document' => $document])
                                        @break
                                        
                                    @case('pengkajian_hukum')
                                        @include('frontend.pembentukan-puu.partials.fields-pengkajian-hukum', ['document' => $document])
                                        @break
                                        
                                    @case('pengkajian_konstitusi')
                                        @include('frontend.pembentukan-puu.partials.fields-pengkajian-konstitusi', ['document' => $document])
                                        @break
                                        
                                    @case('analisis_evaluasi')
                                        @include('frontend.pembentukan-puu.partials.fields-analisis-evaluasi', ['document' => $document])
                                        @break
                                @endswitch
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <i class="fas fa-info-circle text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500 italic">Tidak ada informasi spesifik yang tersedia untuk dokumen ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Action Buttons -->
                    <div class="sticky top-6 sidebar-section">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-6">
                            <h3 class="text-lg font-semibold mb-4">Akses Dokumen</h3>
                            <div class="space-y-3">
                                @if($document->dokumen_utama && Storage::exists($document->dokumen_utama))
                                <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'dokumen']) }}" 
                                   class="block w-full bg-white text-indigo-600 hover:bg-gray-100 font-medium px-4 py-3 rounded-lg text-center transition-all duration-200 flex items-center justify-center gap-2 hover-lift">
                                    <i class="fas fa-file-pdf"></i>
                                    Download Dokumen Utama (PDF)
                                </a>
                                @else
                                <button class="block w-full bg-gray-200 text-gray-500 font-medium px-4 py-3 rounded-lg text-center cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-file-pdf"></i>
                                    Dokumen Tidak Tersedia
                                </button>
                                @endif
                                
                                @if($document->cover && Storage::exists($document->cover))
                                <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'cover']) }}" 
                                   class="block w-full bg-indigo-400 hover:bg-indigo-500 text-white font-medium px-4 py-3 rounded-lg text-center transition-all duration-200 flex items-center justify-center gap-2 hover-lift">
                                    <i class="fas fa-image"></i>
                                    Download Cover
                                </a>
                                @endif
                                
                                @if($document->lampiran && Storage::exists($document->lampiran))
                                <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'lampiran']) }}" 
                                   class="block w-full bg-purple-400 hover:bg-purple-500 text-white font-medium px-4 py-3 rounded-lg text-center transition-all duration-200 flex items-center justify-center gap-2 hover-lift">
                                    <i class="fas fa-paperclip"></i>
                                    Download Lampiran
                                </a>
                                @endif
                                <!-- Tombol Kembali di dalam sidebar -->
                                <a href="{{ url()->previous() }}" 
                                   class="block w-full bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium px-4 py-3 rounded-lg text-center transition-all duration-200 flex items-center justify-center gap-2 hover-lift">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali ke Halaman Sebelumnya
                                </a>
                            </div>
                        </div>

                        <!-- Status & Hak Akses -->
                        <div class="info-card p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-shield-alt mr-2 text-indigo-500"></i>
                                Status & Akses
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">Status Publikasi:</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        @if($document->status_publikasi == 'published') bg-green-100 text-green-800
                                        @elseif($document->status_publikasi == 'draft') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($document->status_publikasi) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">Hak Akses:</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        @if($document->hak_akses == 'public') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($document->hak_akses) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">Kategori:</span>
                                    <span class="text-gray-900">{{ $kategoriLabel }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        @if($document->keterangan)
                        <div class="info-card p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-indigo-500"></i>
                                Keterangan
                            </h3>
                            <p class="text-gray-700 text-sm whitespace-pre-line">
                                {{ $document->keterangan }}
                            </p>
                        </div>
                        @endif

                        <!-- Dokumen Terkait -->
                        @if($relatedDocuments->count() > 0)
                        <div class="info-card p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-link mr-2 text-indigo-500"></i>
                                Dokumen Terkait
                            </h3>
                            <div class="space-y-3">
                                @foreach($relatedDocuments as $related)
                                <a href="{{ route('frontend.pembentukan-puu.show', $related->id) }}" 
                                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200">
                                    <h4 class="font-medium text-gray-900 text-sm mb-1 line-clamp-2">
                                        {{ $related->judul }}
                                    </h4>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ $related->tahun }}
                                        <span class="mx-2">•</span>
                                        <i class="far fa-file mr-1"></i>
                                        {{ $related->jenis_dokumen }}
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer -->
    @if(View::exists('frontend.partials.footer'))
        @include('frontend.partials.footer')
    @endif
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .sticky {
            position: sticky;
        }
    </style>
</body>
</html>