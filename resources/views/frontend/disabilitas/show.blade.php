@extends('components.layouts.frontend')

@section('title', $disabilitas->judul . ' - LAYANAN DISABILITAS')
@section('description', $disabilitas->abstrak ? Str::limit($disabilitas->abstrak, 160) : 'Dokumen disabilitas Kota Kendari')

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
        'lainnya' => 'Lainnya',
    ];
    $statusPublikasiLabels = [
        'draft' => 'Draft',
        'published' => 'Dipublikasikan',
        'uploaded' => 'Telah Diunggah',
        'reviewed' => 'Telah Direview',
        'pending' => 'Ditunda',
        'deleted' => 'Dihapus',
    ];
    $statusDokumenLabels = [
        'berlaku' => 'Berlaku',
        'tidak_berlaku' => 'Tidak Berlaku',
        'mencabut' => 'Mencabut',
        'diubah' => 'Diubah',
        'dicabut' => 'Dicabut',
        'draft' => 'Draft',
    ];
    $ruangLabels = [
        'nasional' => 'Nasional',
        'provinsi' => 'Provinsi',
        'kabupaten_kota' => 'Kabupaten/Kota',
        'internasional' => 'Internasional',
    ];
    $bahasaLabels = [
        'indonesia' => 'Indonesia',
        'inggris' => 'Inggris',
        'daerah' => 'Daerah',
        'lainnya' => 'Lainnya',
    ];
    $aksesLabels = [
        'public' => 'Publik',
        'private' => 'Privat',
        'admin' => 'Admin Only',
        'internal' => 'Internal Only',
        'restricted' => 'Terbatas',
    ];
    $disabilitasLabels = [
        'fisik' => 'Disabilitas Fisik',
        'intelektual' => 'Disabilitas Intelektual',
        'mental' => 'Disabilitas Mental',
        'sensorik' => 'Disabilitas Sensorik',
        'ganda' => 'Disabilitas Ganda/Majemuk',
        'lainnya' => 'Lainnya',
    ];
    $sektorLabels = [
        'pendidikan' => 'Pendidikan',
        'kesehatan' => 'Kesehatan',
        'ketenagakerjaan' => 'Ketenagakerjaan',
        'sosial' => 'Sosial',
        'aksesibilitas' => 'Aksesibilitas',
        'hukum' => 'Hukum & HAM',
        'politik' => 'Politik',
        'lainnya' => 'Lainnya',
    ];

    $jenisLabel = $jenisLabels[$disabilitas->jenis_dokumen] ?? ucfirst($disabilitas->jenis_dokumen);
    $statusDokumen = $statusDokumenLabels[$disabilitas->status_dokumen] ?? ucfirst((string) $disabilitas->status_dokumen);
    $statusPublikasi = $statusPublikasiLabels[$disabilitas->status_publikasi] ?? ucfirst((string) $disabilitas->status_publikasi);
    $hakAkses = $aksesLabels[$disabilitas->hak_akses] ?? ucfirst((string) $disabilitas->hak_akses);

    $fmt = fn($v) => $v ? \Carbon\Carbon::parse($v)->translatedFormat('d F Y') : null;
    $tglPenetapan = $fmt($disabilitas->tanggal_penetapan);
    $tglUnggah = $fmt($disabilitas->tanggal_unggah);

    $jenisDisabilitas = json_decode($disabilitas->jenis_disabilitas, true) ?: [];
    $sektor = json_decode($disabilitas->sektor_kebijakan, true) ?: [];
    $keywords = array_filter(array_map('trim', explode(',', (string) $disabilitas->kata_kunci)));

    // Metadata utama — hanya field yang terisi, metadata internal pindah ke sidebar
    $meta = array_filter([
        'Nomor Dokumen' => $disabilitas->nomor_dokumen,
        'Tahun' => $disabilitas->tahun,
        'Lembaga Penetap' => $disabilitas->lembaga_penetap,
        'Tempat Penetapan' => $disabilitas->tempat_penetapan,
        'Tanggal Penetapan' => $tglPenetapan,
        'Status Dokumen' => $statusDokumen,
        'Ruang Lingkup' => $ruangLabels[$disabilitas->ruang_lingkup] ?? $disabilitas->ruang_lingkup,
        'Bahasa' => $bahasaLabels[$disabilitas->bahasa] ?? $disabilitas->bahasa,
        'Jumlah Halaman' => $disabilitas->jumlah_halaman,
        'Penulis' => $disabilitas->penulis,
        'Penerbit' => $disabilitas->penerbit,
        'Sumber' => $disabilitas->sumber,
        'ISBN/ISSN' => $disabilitas->isbn_issn,
        'DOI' => $disabilitas->doi,
        'Pengunggah' => $disabilitas->pengunggah,
        'Tanggal Unggah' => $tglUnggah,
    ]);

    $authors = collect([
        ['nama' => $disabilitas->lembaga_penetap, 'peran' => 'Lembaga Penetap', 'keterangan' => 'Lembaga yang menetapkan dokumen'],
        ['nama' => $disabilitas->penulis, 'peran' => 'Penulis', 'keterangan' => 'Penulis dokumen'],
        ['nama' => $disabilitas->penerbit, 'peran' => 'Penerbit', 'keterangan' => 'Penerbit dokumen'],
        ['nama' => $disabilitas->pengunggah, 'peran' => 'Pengunggah', 'keterangan' => 'Admin yang mengunggah dokumen'],
        ['nama' => $disabilitas->sumber, 'peran' => 'Sumber', 'keterangan' => 'Sumber dokumen'],
    ])->filter(fn($a) => !empty($a['nama']))->values();

    if ($authors->isEmpty()) {
        $authors = collect([[
            'nama' => 'Pemerintah Kota Kendari',
            'peran' => 'Lembaga Penetap',
            'keterangan' => 'Lembaga pemerintah yang menetapkan dokumen',
        ]]);
    }
@endphp

@section('content')
    <x-frontend.breadcrumb
        :title="__('Detail Dokumen Disabilitas')"
        :listNav="[
            ['label' => __('Layanan Disabilitas'), 'route' => route('frontend.disabilitas.index')],
            ['label' => Str::limit($disabilitas->judul, 50)],
        ]" />

    <section class="relative bg-linear-to-b from-white to-gray-50 py-10 lg:py-14">

        {{-- ORNAMEN ABSTRAK LEMBUT --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
            <div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 animate-rise">

            {{-- Tombol Kembali --}}
            <a wire:navigate.hover href="{{ route('frontend.disabilitas.index') }}"
                class="inline-flex items-center gap-2 mb-6 rounded bg-white ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:ring-primary/40 hover:text-primary">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('Kembali ke Daftar') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== MAIN ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Header --}}
                    <div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                        <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                                <i class="fa-solid fa-file-lines"></i> {{ $jenisLabel }}
                            </span>
                            @if ($disabilitas->status_dokumen)
                                <span class="inline-flex items-center gap-1.5 rounded bg-accent/10 px-3 py-1 text-xs font-semibold text-accent">
                                    <i class="fa-solid fa-circle-check"></i> {{ $statusDokumen }}
                                </span>
                            @endif
                        </div>

                        <h1 class="mt-4 text-xl md:text-2xl lg:text-3xl font-bold text-slate-900 leading-snug">
                            {{ $disabilitas->judul }}
                        </h1>

                        <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
                            @if ($disabilitas->nomor_dokumen)
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-hashtag text-accent"></i> {{ $disabilitas->nomor_dokumen }}</span>
                            @endif
                            @if ($disabilitas->tahun)
                                <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-calendar text-primary"></i> {{ $disabilitas->tahun }}</span>
                            @endif
                            @if ($tglUnggah)
                                <span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-clock text-slate-400"></i> {{ $tglUnggah }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Abstrak --}}
                    @if ($disabilitas->abstrak)
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-align-left text-primary"></i> {{ __('Abstrak / Sinopsis') }}
                            </h2>
                            <p class="abstract-content mt-4 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $disabilitas->abstrak }}</p>
                        </div>
                    @endif

                    {{-- Informasi Dokumen --}}
                    <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                        <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <i class="fa-solid fa-circle-info text-primary"></i> {{ __('Informasi Dokumen') }}
                        </h2>
                        <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach ($meta as $label => $value)
                                <div>
                                    <dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">{{ __($label) }}</dt>
                                    <dd class="mt-0.5 text-sm text-slate-800">
                                        @if ($label === 'DOI')
                                            <a href="https://doi.org/{{ $value }}" target="_blank" rel="noopener"
                                                class="text-accent font-medium hover:text-accent-hover hover:underline">{{ $value }}</a>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>

                        @if ($disabilitas->url_referensi)
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">{{ __('URL Referensi') }}</p>
                                <a href="{{ $disabilitas->url_referensi }}" target="_blank" rel="noopener"
                                    class="mt-1 inline-flex items-center gap-2 text-sm font-medium text-accent transition hover:text-accent-hover hover:underline break-all">
                                    <i class="fa-solid fa-arrow-up-right-from-square shrink-0"></i>
                                    {{ $disabilitas->url_referensi }}
                                </a>
                            </div>
                        @endif

                        @if ($disabilitas->keterangan)
                            <div class="mt-5 rounded border border-slate-100 bg-slate-50/60 p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-accent">{{ __('Keterangan') }}</p>
                                <p class="mt-1 text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $disabilitas->keterangan }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Kata Kunci --}}
                    @if (count($keywords))
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-tags text-primary"></i> {{ __('Kata Kunci / Subjek') }}
                            </h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($keywords as $keyword)
                                    <span class="rounded bg-accent/10 px-3 py-1 text-xs font-medium text-accent">{{ $keyword }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Pengarang / Penanggung Jawab --}}
                    <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                        <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                            <i class="fa-solid fa-user-tie text-primary"></i> {{ __('Informasi Pengarang / Penulis') }}
                        </h2>
                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                        <th class="pb-2 pr-4 font-semibold">{{ __('Nama') }}</th>
                                        <th class="pb-2 pr-4 font-semibold">{{ __('Peran') }}</th>
                                        <th class="pb-2 font-semibold">{{ __('Keterangan') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($authors as $author)
                                        <tr class="transition hover:bg-primary/5">
                                            <td class="py-3 pr-4 font-medium text-slate-800">{{ $author['nama'] }}</td>
                                            <td class="py-3 pr-4">
                                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $author['peran'] }}</span>
                                            </td>
                                            <td class="py-3 text-slate-500">{{ $author['keterangan'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Dokumen Serupa --}}
                    @if ($relatedDocuments->count())
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-link text-primary"></i> {{ __('Dokumen Serupa') }}
                            </h2>
                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($relatedDocuments as $related)
                                    <a wire:navigate.hover href="{{ route('frontend.disabilitas.show', $related->id) }}"
                                        class="group block rounded border border-slate-200 p-4 transition hover:border-primary/40 hover:bg-primary/5">
                                        <h3 class="text-sm font-semibold text-slate-800 line-clamp-2 transition group-hover:text-primary">{{ $related->judul }}</h3>
                                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-400">
                                            <i class="fa-regular fa-calendar"></i> {{ $related->tahun }}
                                            <span>•</span>
                                            {{ $jenisLabels[$related->jenis_dokumen] ?? ucfirst($related->jenis_dokumen) }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Dokumen Terkait (relasi teks) --}}
                    @if ($disabilitas->dokumen_terkait)
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6 lg:p-8">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-diagram-project text-primary"></i> {{ __('Dokumen Terkait') }}
                            </h2>
                            <p class="mt-3 text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $disabilitas->dokumen_terkait }}</p>
                        </div>
                    @endif
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-22 space-y-6">

                        {{-- Akses Dokumen --}}
                        <div class="relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-cloud-arrow-down text-primary"></i> {{ __('Akses Dokumen') }}
                            </h2>
                            <div class="mt-4 space-y-3">
                                @if ($disabilitas->dokumen_utama)
                                    <a href="{{ route('frontend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'dokumen']) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary-hover">
                                        <i class="fa-solid fa-file-pdf"></i> {{ __('Unduh Dokumen (PDF)') }}
                                    </a>
                                    <a href="{{ Storage::url($disabilitas->dokumen_utama) }}" target="_blank" rel="noopener"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-hover">
                                        <i class="fa-solid fa-eye"></i> {{ __('Lihat di Browser') }}
                                    </a>
                                @else
                                    <span class="flex w-full items-center justify-center gap-2 rounded bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                        <i class="fa-solid fa-file-circle-xmark"></i> {{ __('Dokumen Tidak Tersedia') }}
                                    </span>
                                @endif

                                @if ($disabilitas->lampiran)
                                    <a href="{{ route('frontend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'lampiran']) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded bg-white ring-1 ring-accent/40 px-4 py-3 text-sm font-semibold text-accent transition hover:bg-accent/5">
                                        <i class="fa-solid fa-paperclip"></i>
                                        {{ __('Unduh Lampiran') }}
                                        <span class="text-xs font-normal text-slate-400 uppercase">{{ pathinfo($disabilitas->lampiran, PATHINFO_EXTENSION) }}</span>
                                    </a>
                                @endif
                            </div>

                            @if ($disabilitas->dokumen_utama)
                                <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <dt class="text-gray-500">{{ __('Format') }}</dt>
                                        <dd class="font-medium text-slate-800">PDF</dd>
                                    </div>
                                    @if ($disabilitas->jumlah_halaman)
                                        <div class="flex items-center justify-between gap-3">
                                            <dt class="text-gray-500">{{ __('Halaman') }}</dt>
                                            <dd class="font-medium text-slate-800">{{ $disabilitas->jumlah_halaman }}</dd>
                                        </div>
                                    @endif
                                    @if ($disabilitas->bahasa)
                                        <div class="flex items-center justify-between gap-3">
                                            <dt class="text-gray-500">{{ __('Bahasa') }}</dt>
                                            <dd class="font-medium text-slate-800">{{ $bahasaLabels[$disabilitas->bahasa] ?? ucfirst($disabilitas->bahasa) }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            @endif
                        </div>

                        {{-- Status & Akses --}}
                        <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <i class="fa-solid fa-shield-halved text-primary"></i> {{ __('Status & Akses') }}
                            </h2>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-gray-500">{{ __('Jenis Dokumen') }}</dt>
                                    <dd class="text-right font-medium text-slate-800">{{ $jenisLabel }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-gray-500">{{ __('Status Publikasi') }}</dt>
                                    <dd><span class="rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">{{ $statusPublikasi }}</span></dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-gray-500">{{ __('Hak Akses') }}</dt>
                                    <dd>
                                        <span class="inline-flex items-center gap-1.5 rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                            <i class="fa-solid {{ $disabilitas->hak_akses === 'public' ? 'fa-globe' : 'fa-user-lock' }}"></i>
                                            {{ $hakAkses }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Jenis Disabilitas --}}
                        @if (count($jenisDisabilitas))
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                                <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-solid fa-wheelchair text-primary"></i> {{ __('Jenis Disabilitas') }}
                                </h2>
                                <ul class="mt-4 space-y-2">
                                    @foreach ($jenisDisabilitas as $jenis)
                                        <li class="flex items-center gap-2 text-sm text-slate-700">
                                            <i class="fa-solid fa-circle-check text-accent"></i>
                                            {{ $disabilitasLabels[$jenis] ?? ucfirst($jenis) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Sektor Kebijakan --}}
                        @if (count($sektor))
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                                <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-solid fa-layer-group text-primary"></i> {{ __('Sektor Kebijakan') }}
                                </h2>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($sektor as $item)
                                        <span class="rounded bg-primary/10 px-3 py-1 text-xs font-medium text-primary">{{ $sektorLabels[$item] ?? ucfirst($item) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Cover --}}
                        @if ($disabilitas->cover)
                            <div class="bg-white rounded ring-1 ring-slate-200 shadow-sm p-6">
                                <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <i class="fa-regular fa-image text-primary"></i> {{ __('Cover') }}
                                </h2>
                                <a href="{{ Storage::url($disabilitas->cover) }}" target="_blank" rel="noopener"
                                    class="group mt-4 block overflow-hidden rounded ring-1 ring-slate-200 transition hover:ring-primary/40">
                                    <img src="{{ Storage::url($disabilitas->cover) }}"
                                        alt="{{ __('Cover') }} {{ $disabilitas->judul }}" loading="lazy"
                                        class="w-full h-auto transition duration-500 group-hover:scale-[1.02]">
                                </a>
                                <a href="{{ Storage::url($disabilitas->cover) }}" target="_blank" rel="noopener"
                                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-accent transition hover:text-accent-hover">
                                    <i class="fa-solid fa-expand"></i> {{ __('Lihat ukuran penuh') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

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
