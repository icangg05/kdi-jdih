@extends('components.layouts.frontend')

@section('title', $disabilitas->judul . ' - LAYANAN DISABILITAS')
@section('description', $disabilitas->abstrak ? Str::limit($disabilitas->abstrak, 160) : 'Dokumen disabilitas Kota Kendari')

@php
    // Definisikan columnField khusus untuk disabilitas berdasarkan data yang ada di form create
    $columnField = [
        ['Jenis Dokumen', 'jenis_dokumen'],
        ['Judul', 'judul'],
        ['Nomor Dokumen', 'nomor_dokumen'],
        ['Tahun', 'tahun'],
        ['Tempat Penetapan', 'tempat_penetapan'],
        ['Tanggal Penetapan', 'tanggal_penetapan'],
        ['Lembaga Penetap', 'lembaga_penetap'],
        ['Status Dokumen', 'status_dokumen'],
        ['Dokumen Terkait', 'dokumen_terkait'],
        ['Ruang Lingkup', 'ruang_lingkup'],
        ['Abstrak', 'abstrak'],
        ['Kata Kunci', 'kata_kunci'],
        ['Jumlah Halaman', 'jumlah_halaman'],
        ['Bahasa', 'bahasa'],
        ['Penulis', 'penulis'],
        ['Penerbit', 'penerbit'],
        ['ISBN/ISSN', 'isbn_issn'],
        ['DOI', 'doi'],
        ['Sumber', 'sumber'],
        ['URL Referensi', 'url_referensi'],
        ['Status Publikasi', 'status_publikasi'],
        ['Hak Akses', 'hak_akses'],
        ['Tanggal Unggah', 'tanggal_unggah'],
        ['Keterangan', 'keterangan'],
        ['Pengunggah', 'pengunggah'],
        ['Jenis Disabilitas', 'jenis_disabilitas'],
        ['Sektor Kebijakan', 'sektor_kebijakan'],
    ];
@endphp

@section('content')
<div class="bg-linear-to-b from-white via-slate-50 to-slate-100 pb-7">
    <!-- Breadcrumb -->
    <x-frontend.breadcrumb
        :title="$disabilitas->jenis_dokumen . ' Detail'"
        :listNav="[
            ['label' => 'Layanan Disabilitas', 'route' => route('frontend.disabilitas.index')],
            ['label' => Str::limit($disabilitas->judul, 50)],
        ]" />

    <!-- TOP BAR (TOMBOL KEMBALI) -->
    <div class="border-b-2 border-b-gray-200">
        <div class="max-w-6xl mx-auto px-3 lg:px-6 py-4 flex items-center">
            <a href="{{ route('frontend.disabilitas.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-black transition">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- MAIN -->
    <div class="max-w-6xl mx-auto px-3 lg:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT CONTENT -->
            <div class="lg:col-span-2">
                <!-- TITLE -->
                <h1 class="text-2xl font-bold text-slate-800 leading-snug mb-6">
                    {{ $disabilitas->judul }}
                </h1>

                <!-- DETAIL GRID -->
                <div class="text-sm border-t-2 border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
                        @foreach ($columnField as $i => $item)
                            @php
                                $total = count($columnField);
                                $isLast = $i === $total - 1;
                                $isSecondLast = $i === $total - 2;
                                $useThickBorder = $total % 2 === 0 ? $isLast || $isSecondLast : $isLast;
                                
                                // Ambil value dari database
                                $fieldName = $item[1];
                                $value = $disabilitas->$fieldName ?? null;
                                
                                // Format khusus untuk field tertentu
                                if ($fieldName === 'tanggal_penetapan' && $value) {
                                    try {
                                        $value = \Carbon\Carbon::parse($value)->format('j/n/Y');
                                    } catch (\Exception $e) {
                                        $value = $value;
                                    }
                                } elseif ($fieldName === 'tanggal_unggah' && $value) {
                                    try {
                                        $value = \Carbon\Carbon::parse($value)->format('j/n/Y');
                                    } catch (\Exception $e) {
                                        $value = $value;
                                    }
                                } elseif ($fieldName === 'jenis_disabilitas' && $value) {
                                    $decoded = json_decode($value, true);
                                    if (is_array($decoded)) {
                                        $value = implode(', ', array_map(function($item) {
                                            return ucfirst($item);
                                        }, $decoded));
                                    } else {
                                        $value = $decoded;
                                    }
                                } elseif ($fieldName === 'sektor_kebijakan' && $value) {
                                    $decoded = json_decode($value, true);
                                    if (is_array($decoded)) {
                                        $value = implode(', ', array_map(function($item) {
                                            return ucfirst($item);
                                        }, $decoded));
                                    } else {
                                        $value = $decoded;
                                    }
                                } elseif ($fieldName === 'hak_akses') {
                                    $value = $value == 'public' ? 'Publik' : ($value == 'private' ? 'Privat' : ucfirst($value));
                                } elseif ($fieldName === 'status_publikasi') {
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'published' => 'Dipublikasikan',
                                        'uploaded' => 'Telah Diunggah',
                                        'reviewed' => 'Telah Direview',
                                        'pending' => 'Ditunda',
                                        'deleted' => 'Dihapus'
                                    ];
                                    $value = $statusLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'status_dokumen') {
                                    $statusLabels = [
                                        'berlaku' => 'Berlaku',
                                        'tidak_berlaku' => 'Tidak Berlaku',
                                        'mencabut' => 'Mencabut',
                                        'diubah' => 'Diubah',
                                        'dicabut' => 'Dicabut',
                                        'draft' => 'Draft'
                                    ];
                                    $value = $statusLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'jenis_dokumen') {
                                    $jenisLabels = [
                                        'uu' => 'Undang-Undang',
                                        'pp' => 'Peraturan Pemerintah',
                                        'perpres' => 'Peraturan Presiden',
                                        'permen' => 'Peraturan Menteri',
                                        'perda' => 'Peraturan Daerah',
                                        'keppres' => 'Keputusan Presiden',
                                        'kepmen' => 'Keputusan Menteri',
                                        'se' => 'Surat Edaran',
                                        'juknis' => 'Petunjuk Teknis',
                                        'panduan' => 'Panduan',
                                        'laporan' => 'Laporan',
                                        'kajian' => 'Studi/Kajian',
                                        'naskah_akademik' => 'Naskah Akademik',
                                        'rancangan' => 'Rancangan Peraturan',
                                        'lainnya' => 'Lainnya'
                                    ];
                                    $value = $jenisLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'ruang_lingkup') {
                                    $ruangLabels = [
                                        'nasional' => 'Nasional',
                                        'provinsi' => 'Provinsi',
                                        'kabupaten_kota' => 'Kabupaten/Kota',
                                        'internasional' => 'Internasional'
                                    ];
                                    $value = $ruangLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'bahasa') {
                                    $bahasaLabels = [
                                        'indonesia' => 'Indonesia',
                                        'inggris' => 'Inggris',
                                        'daerah' => 'Daerah',
                                        'lainnya' => 'Lainnya'
                                    ];
                                    $value = $bahasaLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'hak_akses') {
                                    $aksesLabels = [
                                        'public' => 'Publik',
                                        'private' => 'Privat',
                                        'admin' => 'Admin Only',
                                        'internal' => 'Internal Only',
                                        'restricted' => 'Terbatas'
                                    ];
                                    $value = $aksesLabels[$value] ?? ucfirst($value);
                                } elseif ($fieldName === 'url_referensi' && $value) {
                                    $value = '<a href="' . e($value) . '" target="_blank" class="text-blue-600 hover:underline">' . e($value) . '</a>';
                                } elseif ($fieldName === 'abstrak' && $value) {
                                    // Abstrak akan ditampilkan di section terpisah
                                    continue;
                                }
                            @endphp

                            @if ($fieldName !== 'abstrak')
                            <div class="{{ $useThickBorder ? 'border-b-2' : 'border-b' }} border-b-gray-200 py-2 lg:py-3.5">
                                <p class="text-accent-hover font-medium">{{ $item[0] }}</p>
                                <p class="font-semibold text-slate-700">
                                    @if($fieldName === 'url_referensi' && filter_var($value, FILTER_VALIDATE_URL))
                                        {!! $value !!}
                                    @else
                                        {{ $value ?: '—' }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- ABSTRAK (Section Terpisah) -->
                    @if($disabilitas->abstrak)
                    <div class="py-6 border-b-2 border-b-gray-200">
                        <p class="text-accent-hover font-medium text-lg mb-3">ABSTRAK / SINOPSIS</p>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            {!! nl2br(e($disabilitas->abstrak)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- DOKUMEN TERKAIT -->
                    <div class="py-4 border-b-2 border-b-gray-200">
                        <p class="text-accent-hover font-medium">Dokumen Terkait</p>
                        <div class="mt-1">
                            @if($disabilitas->dokumen_terkait)
                                <p class="font-medium text-primary">{{ $disabilitas->dokumen_terkait }}</p>
                            @else
                                <p class="italic font-semibold text-slate-400">Tidak ada dokumen terkait</p>
                            @endif
                        </div>
                    </div>

                    <!-- LAMPIRAN / FILE TERKAIT -->
                    <div class="py-4 border-b-2 border-b-gray-200">
                        <p class="text-accent-hover font-medium">Lampiran</p>
                        <div class="mt-1 d-flex left-content-between align-items-start">
                            @if($disabilitas->lampiran)
                                <form action="{{ route('frontend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'lampiran']) }}" method="get">
                                    @csrf
                                    <button type="submit"
                                        class="text-white rounded text-xs px-2.5 py-1.5 bg-accent hover:bg-accent-hover transition">
                                        <i class="fa-solid fa-download mr-1"></i> Download Lampiran
                                    </button>
                                </form>
                                <small class="text-gray-500 ml-3">Format: {{ pathinfo($disabilitas->lampiran, PATHINFO_EXTENSION) }}</small>
                            @else
                                <p class="italic font-semibold text-slate-400">Tidak ada lampiran</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- INFORMASI PENGARANG / PENULIS -->
                <div class="mt-7 text-slate-700">
                    <p class="font-semibold mb-3">INFORMASI PENGARANG / PENULIS</p>

                    <table class="w-full text-sm border border-black/15">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-black/15 px-3 py-2 text-left">Nama</th>
                                <th class="border border-black/15 px-3 py-2 text-left">Peran</th>
                                <th class="border border-black/15 px-3 py-2 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $authors = [];
                                
                                // Lembaga Penetap
                                if ($disabilitas->lembaga_penetap) {
                                    $authors[] = [
                                        'nama' => $disabilitas->lembaga_penetap,
                                        'peran' => 'Lembaga Penetap',
                                        'keterangan' => 'Lembaga yang menetapkan dokumen'
                                    ];
                                }
                                
                                // Penulis
                                if ($disabilitas->penulis) {
                                    $authors[] = [
                                        'nama' => $disabilitas->penulis,
                                        'peran' => 'Penulis',
                                        'keterangan' => 'Penulis dokumen'
                                    ];
                                }
                                
                                // Penerbit
                                if ($disabilitas->penerbit) {
                                    $authors[] = [
                                        'nama' => $disabilitas->penerbit,
                                        'peran' => 'Penerbit',
                                        'keterangan' => 'Penerbit dokumen'
                                    ];
                                }
                                
                                // Pengunggah
                                if ($disabilitas->pengunggah) {
                                    $authors[] = [
                                        'nama' => $disabilitas->pengunggah,
                                        'peran' => 'Pengunggah',
                                        'keterangan' => 'Admin yang mengunggah dokumen'
                                    ];
                                }
                                
                                // Sumber
                                if ($disabilitas->sumber) {
                                    $authors[] = [
                                        'nama' => $disabilitas->sumber,
                                        'peran' => 'Sumber',
                                        'keterangan' => 'Sumber dokumen'
                                    ];
                                }
                                
                                // Default jika tidak ada
                                if (empty($authors)) {
                                    $authors[] = [
                                        'nama' => 'Pemerintah Kota Kendari',
                                        'peran' => 'Lembaga Penetap',
                                        'keterangan' => 'Lembaga pemerintah yang menetapkan dokumen'
                                    ];
                                }
                            @endphp
                            
                            @forelse ($authors as $author)
                                <tr>
                                    <td class="border border-black/15 px-3 py-2 font-medium">{{ $author['nama'] }}</td>
                                    <td class="border border-black/15 px-3 py-2">{{ $author['peran'] }}</td>
                                    <td class="border border-black/15 px-3 py-2 text-gray-600">{{ $author['keterangan'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="border border-black/15 px-3 py-2 text-center text-gray-500">Tidak ada informasi pengarang</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- KATA KUNCI / SUBJEK -->
                @if($disabilitas->kata_kunci)
                <div class="mt-6 text-slate-700">
                    <p class="font-semibold mb-2">KATA KUNCI / SUBJEK</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $disabilitas->kata_kunci) as $keyword)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm border border-gray-300">
                                {{ trim($keyword) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="space-y-6 text-sm">
                <!-- JENIS DOKUMEN -->
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-file-alt mr-2"></i> JENIS DOKUMEN
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $jenisLabels = [
                                'uu' => 'Undang-Undang',
                                'pp' => 'Peraturan Pemerintah',
                                'perpres' => 'Peraturan Presiden',
                                'permen' => 'Peraturan Menteri',
                                'perda' => 'Peraturan Daerah',
                                'keppres' => 'Keputusan Presiden',
                                'kepmen' => 'Keputusan Menteri',
                                'se' => 'Surat Edaran',
                                'juknis' => 'Petunjuk Teknis',
                                'panduan' => 'Panduan',
                                'laporan' => 'Laporan',
                                'kajian' => 'Studi/Kajian',
                                'naskah_akademik' => 'Naskah Akademik',
                                'rancangan' => 'Rancangan Peraturan',
                                'lainnya' => 'Lainnya'
                            ];
                            $jenisDokumen = $jenisLabels[$disabilitas->jenis_dokumen] ?? ucfirst($disabilitas->jenis_dokumen);
                        @endphp
                        <span class="inline-block bg-blue-100 text-blue-800 font-semibold px-4 py-2 rounded-lg text-sm w-full text-center">
                            {{ $jenisDokumen }}
                        </span>
                    </div>
                </div>

                <!-- STATUS PUBLIKASI -->
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-globe mr-2"></i> STATUS PUBLIKASI
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $statusLabels = [
                                'draft' => 'Draft',
                                'published' => 'Dipublikasikan',
                                'uploaded' => 'Telah Diunggah',
                                'reviewed' => 'Telah Direview',
                                'pending' => 'Ditunda',
                                'deleted' => 'Dihapus'
                            ];
                            $status = $statusLabels[$disabilitas->status_publikasi] ?? ucfirst($disabilitas->status_publikasi);
                            $statusColors = [
                                'draft' => 'bg-yellow-100 text-yellow-800',
                                'published' => 'bg-green-100 text-green-800',
                                'uploaded' => 'bg-blue-100 text-blue-800',
                                'reviewed' => 'bg-purple-100 text-purple-800',
                                'pending' => 'bg-orange-100 text-orange-800',
                                'deleted' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$disabilitas->status_publikasi] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block {{ $statusColor }} font-semibold px-4 py-2 rounded-lg text-sm w-full text-center">
                            {{ $status }}
                        </span>
                    </div>
                </div>

                <!-- HAK AKSES -->
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-lock mr-2"></i> HAK AKSES
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $aksesLabels = [
                                'public' => 'Publik',
                                'private' => 'Privat',
                                'admin' => 'Admin Only',
                                'internal' => 'Internal Only',
                                'restricted' => 'Terbatas'
                            ];
                            $hakAkses = $aksesLabels[$disabilitas->hak_akses] ?? ucfirst($disabilitas->hak_akses);
                            $aksesColors = [
                                'public' => 'bg-green-100 text-green-800',
                                'private' => 'bg-red-100 text-red-800',
                                'admin' => 'bg-purple-100 text-purple-800',
                                'internal' => 'bg-orange-100 text-orange-800',
                                'restricted' => 'bg-yellow-100 text-yellow-800'
                            ];
                            $aksesColor = $aksesColors[$disabilitas->hak_akses] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block {{ $aksesColor }} font-semibold px-4 py-2 rounded-lg text-sm w-full text-center">
                            <i class="fa-solid {{ $disabilitas->hak_akses == 'public' ? 'fa-globe' : 'fa-user-lock' }} mr-1"></i>
                            {{ $hakAkses }}
                        </span>
                    </div>
                </div>

                <!-- COVER / THUMBNAIL -->
                @if($disabilitas->cover)
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm overflow-hidden">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100">
                        <i class="fa-solid fa-image mr-2"></i> COVER
                    </div>
                    <div class="p-4">
                        <img src="{{ Storage::url($disabilitas->cover) }}" 
                             alt="Cover {{ $disabilitas->judul }}" 
                             class="w-full h-auto rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="mt-3 text-center">
                            <a href="{{ Storage::url($disabilitas->cover) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fa-solid fa-expand mr-1"></i> Lihat ukuran penuh
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- DOKUMEN UTAMA -->
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-file-pdf mr-2"></i> DOKUMEN UTAMA
                    </div>
                    <div class="px-4 py-4 space-y-3">
                        <!-- Download Dokumen -->
                        @if($disabilitas->dokumen_utama)
                        <a href="{{ route('frontend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'dokumen']) }}"
                           class="rounded-lg flex justify-center items-center gap-2 text-center bg-accent text-white
                           font-semibold py-3 hover:bg-accent-hover transition w-full no-underline shadow hover:shadow-md">
                            <i class="fa-solid fa-download"></i> Download PDF
                        </a>
                        
                        <!-- View PDF -->
                        <a href="{{ Storage::url($disabilitas->dokumen_utama) }}" 
                           target="_blank"
                           class="rounded-lg flex justify-center items-center gap-2 text-center bg-primary text-white
                           font-semibold py-3 hover:bg-primary-hover transition w-full no-underline shadow hover:shadow-md">
                            <i class="fa-solid fa-eye"></i> Lihat di Browser
                        </a>
                        
                        <!-- Info File -->
                        <div class="text-xs text-gray-600 mt-2 p-2 bg-gray-100 rounded">
                            <div class="flex justify-between">
                                <span>Format:</span>
                                <span class="font-semibold">PDF</span>
                            </div>
                            @if($disabilitas->jumlah_halaman)
                            <div class="flex justify-between mt-1">
                                <span>Halaman:</span>
                                <span class="font-semibold">{{ $disabilitas->jumlah_halaman }}</span>
                            </div>
                            @endif
                            @if($disabilitas->bahasa)
                            <div class="flex justify-between mt-1">
                                <span>Bahasa:</span>
                                <span class="font-semibold">{{ ucfirst($disabilitas->bahasa) }}</span>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fa-solid fa-file-circle-xmark text-3xl text-gray-400 mb-2"></i>
                            <p class="font-semibold text-gray-500">Dokumen tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- INFORMASI SINGKAT -->
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-info-circle mr-2"></i> INFORMASI SINGKAT
                    </div>
                    <div class="px-4 py-4 space-y-4">
                        <!-- Nomor & Tahun -->
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Nomor Dokumen</p>
                            <p class="font-semibold text-lg">{{ $disabilitas->nomor_dokumen }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Tahun</p>
                            <p class="font-semibold text-lg">{{ $disabilitas->tahun }}</p>
                        </div>
                        
                        <!-- Tanggal -->
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Tanggal Unggah</p>
                            <p class="font-semibold">
                                @if($disabilitas->tanggal_unggah)
                                    {{ \Carbon\Carbon::parse($disabilitas->tanggal_unggah)->format('j/n/Y') }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        
                        @if($disabilitas->tanggal_penetapan)
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Tanggal Penetapan</p>
                            <p class="font-semibold">
                                {{ \Carbon\Carbon::parse($disabilitas->tanggal_penetapan)->format('j/n/Y') }}
                            </p>
                        </div>
                        @endif
                        
                        <!-- Identifikasi -->
                        @if($disabilitas->isbn_issn)
                        <div>
                            <p class="text-gray-600 text-xs mb-1">ISBN/ISSN</p>
                            <p class="font-semibold">{{ $disabilitas->isbn_issn }}</p>
                        </div>
                        @endif
                        
                        @if($disabilitas->doi)
                        <div>
                            <p class="text-gray-600 text-xs mb-1">DOI</p>
                            <p class="font-semibold">
                                <a href="https://doi.org/{{ $disabilitas->doi }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $disabilitas->doi }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        <!-- URL Referensi -->
                        @if($disabilitas->url_referensi)
                        <div>
                            <p class="text-gray-600 text-xs mb-1">URL Referensi</p>
                            <p class="font-semibold truncate">
                                <a href="{{ $disabilitas->url_referensi }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                    <i class="fa-solid fa-link mr-1"></i> Kunjungi Sumber
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- JENIS DISABILITAS -->
                @if($disabilitas->jenis_disabilitas)
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-wheelchair mr-2"></i> JENIS DISABILITAS
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $jenisDisabilitas = json_decode($disabilitas->jenis_disabilitas, true) ?? [];
                            $jenisLabels = [
                                'fisik' => 'Disabilitas Fisik',
                                'intelektual' => 'Disabilitas Intelektual',
                                'mental' => 'Disabilitas Mental',
                                'sensorik' => 'Disabilitas Sensorik',
                                'ganda' => 'Disabilitas Ganda/Majemuk',
                                'lainnya' => 'Lainnya'
                            ];
                        @endphp
                        <div class="space-y-2">
                            @foreach($jenisDisabilitas as $jenis)
                                <div class="flex items-center">
                                    <i class="fa-solid fa-check text-green-600 mr-2"></i>
                                    <span class="text-sm">{{ $jenisLabels[$jenis] ?? ucfirst($jenis) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- SEKTOR KEBIJAKAN -->
                @if($disabilitas->sektor_kebijakan)
                <div class="border border-gray-200 bg-gray-50 rounded-lg shadow-sm">
                    <div class="border-b-2 border-b-gray-200 px-4 py-3 font-semibold text-gray-700 bg-gray-100 rounded-t-lg">
                        <i class="fa-solid fa-chart-pie mr-2"></i> SEKTOR KEBIJAKAN
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $sektor = json_decode($disabilitas->sektor_kebijakan, true) ?? [];
                            $sektorLabels = [
                                'pendidikan' => 'Pendidikan',
                                'kesehatan' => 'Kesehatan',
                                'ketenagakerjaan' => 'Ketenagakerjaan',
                                'sosial' => 'Sosial',
                                'aksesibilitas' => 'Aksesibilitas',
                                'hukum' => 'Hukum & HAM',
                                'politik' => 'Politik',
                                'lainnya' => 'Lainnya'
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($sektor as $item)
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs border border-blue-200">
                                    {{ $sektorLabels[$item] ?? ucfirst($item) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-accent {
        color: #4f46e5;
    }
    
    .text-accent-hover {
        color: #4f46e5;
    }
    
    .text-primary {
        color: #3b82f6;
    }
    
    .bg-accent {
        background-color: #4f46e5;
    }
    
    .bg-accent-hover:hover {
        background-color: #4338ca;
    }
    
    .bg-primary {
        background-color: #3b82f6;
    }
    
    .bg-primary-hover:hover {
        background-color: #2563eb;
    }
    
    .bg-blue-100 {
        background-color: #dbeafe;
    }
    
    .bg-green-100 {
        background-color: #d1fae5;
    }
    
    .bg-red-100 {
        background-color: #fee2e2;
    }
    
    .bg-yellow-100 {
        background-color: #fef3c7;
    }
    
    .bg-purple-100 {
        background-color: #e9d5ff;
    }
    
    .bg-orange-100 {
        background-color: #ffedd5;
    }
    
    .bg-gray-100 {
        background-color: #f3f4f6;
    }
    
    .text-blue-800 {
        color: #1e40af;
    }
    
    .text-green-800 {
        color: #065f46;
    }
    
    .text-red-800 {
        color: #991b1b;
    }
    
    .text-yellow-800 {
        color: #92400e;
    }
    
    .text-purple-800 {
        color: #5b21b6;
    }
    
    .text-orange-800 {
        color: #9a3412;
    }
    
    .text-gray-800 {
        color: #1f2937;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Voice panel functionality
        if (window.Alpine && Alpine.store('voicePanelEnabled')) {
            const title = document.querySelector('h1')?.textContent;
            const abstract = document.querySelector('.abstract-content')?.textContent;
            
            if (title && abstract) {
                setTimeout(() => {
                    speak(`Dokumen disabilitas: ${title}. ${abstract.substring(0, 200)}...`);
                }, 1000);
            }
        }
        
        // Function untuk text-to-speech
        function speak(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                speechSynthesis.speak(utterance);
            }
        }
        
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endpush