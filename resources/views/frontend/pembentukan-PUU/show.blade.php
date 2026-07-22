@extends('components.layouts.frontend')

@section('title', $document->judul . ' - JDIH Kota Kendari')
@section('description', Str::limit(strip_tags($document->abstrak ?? $document->judul), 160))

@php
    $jenisLabels = [
        'naskah_akademik'              => 'Naskah Akademik',
        'naskah_keterangan_penjelasan' => 'Naskah Keterangan dan/atau Penjelasan',
        'rancangan_puu'                => 'Rancangan PUU',
        'penelitian_hukum'             => 'Penelitian Hukum',
        'pengkajian_hukum'             => 'Pengkajian Hukum',
        'pengkajian_konstitusi'        => 'Pengkajian Konstitusi',
        'analisis_evaluasi'            => 'Analisis & Evaluasi',
    ];
    $jenisLabel = $jenisLabels[$document->jenis_dokumen] ?? $document->jenis_dokumen;

    $tahapanLabels = [
        'pra_legislasi'  => 'Pra-Legislasi',
        'penyusunan_ruu' => 'Penyusunan RUU',
        'pembahasan_dpr' => 'Pembahasan DPR',
        'pengundangan'   => 'Pengundangan',
        'evaluasi'       => 'Evaluasi',
    ];
    $tahapanLabel = $tahapanLabels[$document->tahapan_pembentukan] ?? $document->tahapan_pembentukan;

    // Field spesifik per jenis dokumen (label manusiawi) — render inline, tanpa partial
    $specificMap = [
        'naskah_akademik' => [
            'rumusan_masalah' => 'Rumusan Masalah', 'tujuan_penelitian' => 'Tujuan Penelitian',
            'metodologi_penelitian' => 'Metodologi Penelitian', 'tim_penyusun' => 'Tim Penyusun',
            'tanggal_penyelesaian' => 'Tanggal Penyelesaian',
        ],
        'penelitian_hukum' => [
            'latar_belakang' => 'Latar Belakang', 'fokus_penelitian' => 'Fokus Penelitian',
            'hasil_penelitian' => 'Hasil Penelitian', 'rekomendasi' => 'Rekomendasi',
            'lokasi_penelitian' => 'Lokasi Penelitian',
        ],
        'rancangan_puu' => [
            'jenis_rancangan' => 'Jenis Rancangan', 'prolegnas' => 'Prolegnas',
            'inisiator' => 'Inisiator', 'pansus_panja' => 'Pansus/Panja', 'tanggal_pengajuan' => 'Tanggal Pengajuan',
        ],
        'pengkajian_hukum' => [
            'objek_pengkajian' => 'Objek Pengkajian', 'jenis_pengkajian' => 'Jenis Pengkajian',
            'tujuan_pengkajian' => 'Tujuan Pengkajian', 'kesimpulan_pengkajian' => 'Kesimpulan Pengkajian',
            'tanggal_pengkajian' => 'Tanggal Pengkajian',
        ],
        'pengkajian_konstitusi' => [
            'aspek_konstitusi' => 'Aspek Konstitusi', 'jenis_pengkajian_konstitusi' => 'Jenis Pengkajian',
            'dasar_hukum_pengkajian' => 'Dasar Hukum Pengkajian', 'instansi_pengkaji' => 'Instansi Pengkaji',
            'implikasi_konstitusional' => 'Implikasi Konstitusional',
        ],
        'analisis_evaluasi' => [
            'objek_evaluasi' => 'Objek Evaluasi', 'metode_evaluasi' => 'Metode Evaluasi',
            'indikator_evaluasi' => 'Indikator Evaluasi', 'temuan_evaluasi' => 'Temuan Evaluasi',
            'rekomendasi_perbaikan' => 'Rekomendasi Perbaikan', 'periode_evaluasi' => 'Periode Evaluasi',
        ],
    ];
    $specificFields = collect($specificMap[$document->jenis_dokumen] ?? [])
        ->filter(fn($label, $field) => !empty($document->$field));

    $hasPdf      = $document->dokumen_utama && Storage::disk('public')->exists($document->dokumen_utama);
    $hasCover    = $document->cover && Storage::disk('public')->exists($document->cover);
    $hasLampiran = $document->lampiran && Storage::disk('public')->exists($document->lampiran);
    $tglUnggah   = $document->tanggal_unggah ? \Carbon\Carbon::parse($document->tanggal_unggah)->translatedFormat('d F Y') : '-';
@endphp

@section('content')
    <x-frontend.breadcrumb
        :title="$kategoriLabel"
        :listNav="[
            ['label' => __('Pembentukan PUU'), 'route' => route('frontend.pembentukan-puu.index')],
            ['label' => Str::limit($document->judul, 50)],
        ]" />

    <section class="relative bg-linear-to-b from-white to-gray-50 py-10 lg:py-14">

        {{-- ORNAMEN ABSTRAK LEMBUT --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
            <div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4">

            {{-- Tombol Kembali --}}
            <a wire:navigate.hover href="{{ route('frontend.pembentukan-puu.kategori', $kategoriValue) }}"
                class="inline-flex items-center gap-2 mb-6 rounded bg-white ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:ring-primary/40 hover:text-primary">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('Kembali') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== MAIN ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Header --}}
                    <div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                        <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                                <i class="fa-solid fa-file-pen"></i> {{ $jenisLabel }}
                            </span>
                            @if ($document->tahun)
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                    <i class="fa-regular fa-calendar"></i> {{ $document->tahun }}
                                </span>
                            @endif
                        </div>

                        <h1 class="mt-4 text-xl md:text-2xl lg:text-3xl font-bold text-slate-900 leading-snug">
                            {{ $document->judul }}
                        </h1>

                        <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-eye text-accent"></i> {{ $document->views ?? 0 }} {{ __('dilihat') }}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-download text-primary"></i> {{ $document->jumlah_download ?? 0 }} {{ __('unduh') }}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-clock text-slate-400"></i> {{ $tglUnggah }}</span>
                        </div>
                    </div>

                    {{-- Informasi Dokumen --}}
                    <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <i class="fa-solid fa-circle-info text-primary"></i> {{ __('Informasi Dokumen') }}
                        </h3>
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach ([
                                __('Jenis Dokumen') => $jenisLabel,
                                __('Nomor Dokumen') => $document->nomor_dokumen ?: '-',
                                __('Lembaga Pemrakarsa') => $document->lembaga_pemrakarsa ?: '-',
                                __('Status Dokumen') => $document->status_dokumen ?: '-',
                                __('Tahapan Pembentukan') => $tahapanLabel ?: '-',
                                __('Penulis/Penyusun') => $document->penulis ?: '-',
                                __('Pengunggah') => $document->pengunggah ?: '-',
                                __('Tanggal Unggah') => $tglUnggah,
                            ] as $label => $value)
                                <div>
                                    <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">{{ $label }}</p>
                                    <p class="mt-0.5 text-sm text-slate-800">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Abstrak --}}
                    @if ($document->abstrak)
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-file-lines text-primary"></i> {{ __('Abstrak / Ringkasan') }}
                            </h3>
                            <p class="mt-4 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $document->abstrak }}</p>
                        </div>
                    @endif

                    {{-- Kata Kunci --}}
                    @if ($document->kata_kunci)
                        @php $keywords = array_filter(array_map('trim', explode(',', $document->kata_kunci))); @endphp
                        @if (count($keywords))
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-solid fa-tags text-primary"></i> {{ __('Kata Kunci') }}
                                </h3>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($keywords as $keyword)
                                        <span class="rounded bg-accent/10 px-3 py-1 text-xs font-medium text-accent">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Informasi Spesifik --}}
                    @if ($specificFields->isNotEmpty())
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-clipboard-list text-primary"></i> {{ __('Informasi Spesifik') }} — {{ $jenisLabel }}
                            </h3>
                            <div class="mt-5 space-y-4">
                                @foreach ($specificFields as $field => $label)
                                    <div class="rounded border border-slate-100 bg-slate-50/60 p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-accent">{{ $label }}</p>
                                        <p class="mt-1 text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $document->$field }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-22 space-y-6">

                        {{-- Akses Dokumen --}}
                        <div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
                            <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-cloud-arrow-down text-primary"></i> {{ __('Akses Dokumen') }}
                            </h3>
                            <div class="mt-4 space-y-3">
                                @if ($hasPdf)
                                    <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'dokumen']) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary-hover">
                                        <i class="fa-solid fa-file-pdf"></i> {{ __('Unduh Dokumen (PDF)') }}
                                    </a>
                                @else
                                    <span class="flex w-full items-center justify-center gap-2 rounded bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                        <i class="fa-solid fa-file-circle-xmark"></i> {{ __('Dokumen Tidak Tersedia') }}
                                    </span>
                                @endif

                                @if ($hasCover)
                                    <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'cover']) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-hover">
                                        <i class="fa-solid fa-image"></i> {{ __('Unduh Cover') }}
                                    </a>
                                @endif

                                @if ($hasLampiran)
                                    <a href="{{ route('frontend.pembentukan-puu.download', ['id' => $document->id, 'type' => 'lampiran']) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-white ring-1 ring-accent/40 px-4 py-3 text-sm font-semibold text-accent transition hover:bg-accent/5">
                                        <i class="fa-solid fa-paperclip"></i> {{ __('Unduh Lampiran') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Status & Akses --}}
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-shield-halved text-primary"></i> {{ __('Status & Akses') }}
                            </h3>
                            <div class="mt-4 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-gray-500">{{ __('Status Publikasi') }}</span>
                                    <span class="rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">{{ ucfirst($document->status_publikasi) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-gray-500">{{ __('Hak Akses') }}</span>
                                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">{{ ucfirst($document->hak_akses) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-gray-500">{{ __('Kategori') }}</span>
                                    <span class="text-slate-800 font-medium text-right">{{ $kategoriLabel }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        @if ($document->keterangan)
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-solid fa-note-sticky text-primary"></i> {{ __('Keterangan') }}
                                </h3>
                                <p class="mt-3 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $document->keterangan }}</p>
                            </div>
                        @endif

                        {{-- Dokumen Terkait --}}
                        @if ($relatedDocuments->count() > 0)
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-solid fa-link text-primary"></i> {{ __('Dokumen Terkait') }}
                                </h3>
                                <div class="mt-4 space-y-3">
                                    @foreach ($relatedDocuments as $related)
                                        <a wire:navigate.hover href="{{ route('frontend.pembentukan-puu.show', $related->id) }}"
                                            class="group block rounded border border-slate-200 p-3 transition hover:border-primary/40 hover:bg-primary/5">
                                            <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 group-hover:text-primary">{{ $related->judul }}</h4>
                                            <div class="mt-1.5 flex items-center gap-2 text-xs text-slate-400">
                                                <i class="fa-regular fa-calendar"></i> {{ $related->tahun }}
                                                <span>•</span>
                                                {{ $jenisLabels[$related->jenis_dokumen] ?? $related->jenis_dokumen }}
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
    </section>
@endsection
