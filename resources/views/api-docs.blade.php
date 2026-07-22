<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Dokumentasi API JDIH Kota Kendari') }} v1.0</title>
    <meta name="description" content="{{ __('Dokumentasi REST API JDIH Kota Kendari untuk integrasi dengan JDIHN Nasional.') }}">
    @vite('resources/css/app.css')
    <style>
        /* Active state sidebar — warna oranye (primary), berbeda dari hover (biru/accent) */
        .nav-link.active,
        .nav-link.active:hover {
            background-color: color-mix(in srgb, var(--color-primary) 12%, transparent);
            color: var(--color-primary);
            font-weight: 600;
        }
        .nav-link.active .nav-dot { background-color: var(--color-primary); }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 antialiased">

@php
    $nav = [
        'info'          => __('Informasi API'),
        'auth'          => __('Authentication'),
        'endpoints'     => __('Endpoint'),
        'errors'        => __('Error Responses'),
        'schema'        => __('Struktur Data'),
        'testing'       => __('Testing'),
        'ratelimit'     => __('Rate Limiting'),
        'policy'        => __('Kebijakan API Key'),
        'bestpractices' => __('Best Practices'),
        'contact'       => __('Kontak & Support'),
        'jdihn'         => __('Catatan JDIHN'),
    ];
@endphp

{{-- ============ HEADER ============ --}}
<header class="border-b border-slate-200 bg-white">
    <div class="h-1 w-full bg-linear-to-r from-primary via-primary to-accent"></div>
    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
        <div class="flex items-start gap-4">
            <img src="{{ asset('assets/img/jdih-logo.png') }}" alt="Logo JDIH Kota Kendari" class="h-12 w-auto shrink-0">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        {{ __('Dokumentasi API JDIH Kota Kendari') }}
                    </h1>
                    <span class="inline-flex items-center rounded-full bg-accent/10 px-2.5 py-0.5 text-xs font-semibold text-accent ring-1 ring-inset ring-accent/20">v1.0</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary ring-1 ring-inset ring-primary/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary"></span> {{ __('AKTIF') }}
                    </span>
                </div>
                <p class="mt-2 max-w-prose text-slate-500">{{ __('Untuk integrasi dengan JDIHN Nasional — REST API, format JSON.') }}</p>
            </div>
        </div>
        <div class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
            <div class="font-mono text-[11px] font-medium uppercase tracking-wide text-slate-400">Base URL</div>
            <div class="mt-0.5 font-mono font-medium text-accent">https://jdih.kendarikota.go.id/api/jdih/</div>
        </div>
    </div>
</header>

<div class="mx-auto flex max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:px-8">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="hidden w-56 shrink-0 lg:block">
        <nav class="sticky top-8 space-y-1">
            <div class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('Navigasi') }}</div>
            @foreach($nav as $id => $label)
                <a href="#{{ $id }}" data-nav="{{ $id }}" class="nav-link group flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-accent/5 hover:text-accent">
                    <span class="nav-dot h-1.5 w-1.5 shrink-0 rounded-full bg-slate-300 transition group-hover:bg-accent"></span>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- ============ MAIN ============ --}}
    <main class="min-w-0 flex-1 space-y-14">

        {{-- INFO --}}
        <section id="info" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Informasi API') }}</h2>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Base URL Production') }}</dt>
                    <dd class="mt-1.5 break-all font-mono text-sm text-slate-800">https://jdih.kendarikota.go.id/api/jdih/</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Base URL Testing') }}</dt>
                    <dd class="mt-1.5 break-all font-mono text-sm text-slate-800">http://localhost:8000/api/jdih/</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Format') }}</dt>
                    <dd class="mt-1.5 font-mono text-sm text-slate-800">JSON</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Authentication') }}</dt>
                    <dd class="mt-1.5 font-mono text-sm text-slate-800">API Key — Header: X-API-Key</dd>
                </div>
            </dl>
        </section>

        {{-- AUTH --}}
        <section id="auth" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Authentication & API Keys') }}</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="relative overflow-hidden rounded-xl border border-primary/30 bg-primary/5 p-5 shadow-sm">
                    <span class="absolute right-0 top-0 rounded-bl-lg bg-primary px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white">{{ __('Rekomendasi') }}</span>
                    <h3 class="flex items-center gap-2 pr-24 font-semibold text-slate-900">
                        <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 5.82 21l1.18-6.88-5-4.87 7.1-1.01z"/></svg>
                        {{ __('API Key Permanen untuk JDIHN') }}
                    </h3>
                    <div class="mt-3 rounded-lg bg-white px-3 py-2 font-mono text-sm text-slate-800 ring-1 ring-primary/25">jdih_kendari_jdihn_permanent</div>
                    <ul class="mt-3 space-y-1 text-sm text-slate-600">
                        <li><strong class="font-semibold text-slate-800">{{ __('Status:') }}</strong> {{ __('PERMANEN — tidak pernah kadaluarsa') }}</li>
                        <li><strong class="font-semibold text-slate-800">{{ __('Rate limit:') }}</strong> {{ __('1000 request/jam') }}</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="font-semibold text-slate-800">{{ __('API Key Legacy') }} <span class="text-sm font-normal text-slate-400">{{ __('(tetap didukung)') }}</span></h3>
                    <div class="mt-3 rounded-lg bg-slate-50 px-3 py-2 font-mono text-sm text-slate-800 ring-1 ring-slate-200">jdih_kendari_jdihn_2025_001</div>
                    <ul class="mt-3 space-y-1 text-sm text-slate-500">
                        <li><strong class="font-semibold text-slate-700">{{ __('Valid hingga:') }}</strong> {{ __('31 Desember 2030') }}</li>
                        <li><strong class="font-semibold text-slate-700">{{ __('Rate limit:') }}</strong> {{ __('1000 request/jam') }}</li>
                    </ul>
                </div>
            </div>

            <h3 class="mt-6 mb-2 font-semibold text-slate-800">{{ __('Cara Penggunaan') }}</h3>
            @include('partials.code', ['label' => 'bash', 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/health" \
     -H "X-API-Key: jdih_kendari_jdihn_permanent"'])

            <div class="mt-6 rounded-xl border border-primary/25 bg-primary/5 p-5">
                <h3 class="flex items-center gap-2 font-semibold text-slate-900">
                    <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    {{ __('Catatan Penting') }}
                </h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600 marker:text-primary">
                    <li>{!! __('Gunakan <strong class="font-semibold text-slate-800">jdih_kendari_jdihn_permanent</strong> untuk integrasi jangka panjang') !!}</li>
                    <li>{{ __('Key ini tidak akan kadaluarsa secara otomatis') }}</li>
                    <li>{{ __('Monitoring dan security audit dilakukan berkala') }}</li>
                    <li>{{ __('Key dapat dinonaktifkan manual jika ditemukan penyalahgunaan') }}</li>
                </ul>
            </div>
        </section>

        {{-- ENDPOINTS --}}
        <section id="endpoints" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Endpoint') }}</h2>
            </div>
            <div class="space-y-6">

                {{-- 1 health --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/health</code>
                        <span class="text-sm text-slate-400">Health Check</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mengecek status API. Tidak perlu authentication.') }}</p>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/health"'])
                        @include('partials.code', ['label' => __('Response'), 'code' => '{
  "status": "success",
  "service": "JDIH Kota Kendari API",
  "version": "1.0.0",
  "timestamp": "2026-01-18 11:12:00"
}'])
                    </div>
                </article>

                {{-- 2 search --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/search</code>
                        <span class="text-sm text-slate-400">Search Documents</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mencari dokumen hukum di database JDIH.') }}</p>
                        <div>
                            <h4 class="mb-2 text-sm font-semibold text-slate-700">{{ __('Parameter') }}</h4>
                            <ul class="space-y-1.5 text-sm">
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">q</code> <span class="font-medium text-red-600">{{ __('required') }}</span> — {{ __('Kata kunci pencarian') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">limit</code> <span class="text-slate-400">{{ __('optional, default 20') }}</span> — {{ __('Jumlah hasil per halaman') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">page</code> <span class="text-slate-400">{{ __('optional, default 1') }}</span> — {{ __('Nomor halaman') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">tahun</code> <span class="text-slate-400">{{ __('optional') }}</span> — {{ __('Filter tahun terbit') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">tipe_dokumen</code> <span class="text-slate-400">{{ __('optional') }}</span> — {{ __('ID tipe dokumen (1-132)') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">status</code> <span class="text-slate-400">{{ __('optional') }}</span> — Berlaku, Dicabut, Tidak Berlaku, Diubah</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">bidang_hukum</code> <span class="text-slate-400">{{ __('optional') }}</span> — {{ __('Bidang hukum') }}</li>
                                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">sort</code> <span class="text-slate-400">{{ __('optional') }}</span> — terbaru, terlama, relevansi</li>
                            </ul>
                        </div>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/search?q=disabilitas&limit=5&tahun=2024" \
-H "X-API-Key: jdih_kendari_jdihn_permanent"'])
                        @include('partials.code', ['label' => __('Response'), 'code' => '{
  "success": true,
  "data": [
    {
      "id": 123,
      "judul": "Peraturan Daerah tentang Hak Penyandang Disabilitas",
      "nomor_peraturan": "12",
      "tahun_terbit": 2024,
      "tipe_dokumen_nama": "PERATURAN DAERAH",
      "tipe_dokumen_singkatan": "PERDA",
      "status": "Berlaku",
      "abstrak": "Peraturan ini mengatur tentang hak-hak penyandang disabilitas...",
      "bidang_hukum": "Sosial",
      "detail_url": "https://jdih.kendarikota.go.id/api/jdih/documents/123",
      "download_url": "https://jdih.kendarikota.go.id/api/jdih/documents/123/download"
    }
  ],
  "pagination": {
    "total": 15,
    "page": 1,
    "limit": 5,
    "total_pages": 3,
    "has_more": true
  }
}'])
                    </div>
                </article>

                {{-- 3 detail --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/documents/{id}</code>
                        <span class="text-sm text-slate-400">Document Details</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mendapatkan detail lengkap dokumen berdasarkan ID.') }}</p>
                        <p class="text-sm"><strong>{{ __('Path:') }}</strong> <code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">id</code> <span class="font-medium text-red-600">{{ __('required') }}</span> — {{ __('ID dokumen') }}</p>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/documents/123" \
-H "X-API-Key: jdih_kendari_jdihn_permanent"'])
                        @include('partials.code', ['label' => __('Response (partial)'), 'code' => '{
  "success": true,
  "data": {
    "metadata": {
      "tipe_dokumen": { "id": 1, "nama": "PERATURAN DAERAH", "singkatan": "PERDA" },
      "judul": "Peraturan Daerah Kota Kendari Nomor 8 Tahun 2025",
      "nomor_peraturan": "8",
      "tahun_terbit": 2025,
      "bentuk_peraturan": "Peraturan Daerah",
      "jenis_peraturan": "Umum"
    },
    "informasi_publikasi": {
      "tempat_terbit": "Kendari",
      "penerbit": "Pemerintah Kota Kendari",
      "sumber": "Lembaran Daerah"
    },
    "tanggal_penting": {
      "tanggal_penetapan": "2025-05-15",
      "tanggal_pengundangan": "2025-05-20",
      "created_at": "2025-05-20T10:30:00Z"
    },
    "kategorisasi": {
      "bidang_hukum": "Ketahanan Pangan",
      "bahasa": "Indonesia",
      "klasifikasi": "340 - Hukum"
    },
    "status_dokumen": { "status": "Berlaku", "berkekuatan_hukum_tetap": "Ya" },
    "statistik": { "hit_see": 150, "hit_download": 45 }
  }
}'])
                    </div>
                </article>

                {{-- 4 download --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/documents/{id}/download</code>
                        <span class="text-sm text-slate-400">Document Download</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mendownload file dokumen (format PDF).') }}</p>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/documents/123/download" \
-H "X-API-Key: jdih_kendari_jdihn_permanent" \
--output document.pdf'])
                        @include('partials.code', ['label' => __('Response'), 'code' => '{
  "success": true,
  "message": "Download request recorded.",
  "data": {
    "id": 123,
    "judul": "Peraturan Daerah Kota Kendari Nomor 8 Tahun 2025",
    "download_info": {
      "timestamp": "2026-01-18T11:12:00Z",
      "download_id": "550e8400-e29b-41d4-a716-446655440000"
    }
  }
}'])
                    </div>
                </article>

                {{-- 5 statistics --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/statistics</code>
                        <span class="text-sm text-slate-400">Statistics</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mendapatkan statistik sistem lengkap.') }}</p>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/statistics" \
-H "X-API-Key: jdih_kendari_jdihn_permanent"'])
                        @include('partials.code', ['label' => __('Response (partial)'), 'code' => '{
  "success": true,
  "data": {
    "total_dokumen": 722,
    "total_views": 39207,
    "total_downloads": 4150,
    "dokumen_per_tahun": [
      {"tahun": 2025, "total": 45},
      {"tahun": 2024, "total": 38},
      {"tahun": 2023, "total": 32}
    ],
    "dokumen_per_tipe": [
      {"tipe": "PERATURAN DAERAH", "total": 320},
      {"tipe": "PERATURAN WALIKOTA", "total": 195}
    ],
    "most_viewed": [
      {"id": 123, "judul": "Perda tentang Disabilitas", "hit_see": 450}
    ]
  }
}'])
                    </div>
                </article>

                {{-- 6 document-types --}}
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-accent/40 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                        <x-api-method>GET</x-api-method>
                        <code class="font-mono text-sm font-semibold text-slate-800">/document-types</code>
                        <span class="text-sm text-slate-400">Document Types</span>
                    </div>
                    <div class="space-y-4 p-5">
                        <p class="text-sm">{{ __('Mendapatkan daftar hierarki tipe dokumen yang tersedia.') }}</p>
                        @include('partials.code', ['label' => __('Request'), 'code' => 'curl -X GET "https://jdih.kendarikota.go.id/api/jdih/document-types" \
-H "X-API-Key: jdih_kendari_jdihn_permanent"'])
                    </div>
                </article>

            </div>
        </section>

        {{-- ERRORS --}}
        <section id="errors" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Error Responses') }}</h2>
            </div>
            <div class="space-y-5">
                @php
                    $errors = [
                        ['400', 'Bad Request', '{
  "success": false,
  "message": "Validation error",
  "errors": { "q": ["The search query is required"] },
  "error_code": "VAL_001"
}'],
                        ['401', 'Missing API Key', '{
  "success": false,
  "message": "API key is required",
  "error_code": "API_001"
}'],
                        ['403', 'Invalid API Key', '{
  "success": false,
  "message": "Invalid api key",
  "error_code": "API_002",
  "hint": "Please use a valid API key provided by JDIH Kota Kendari"
}'],
                        ['404', 'Not Found', '{
  "success": false,
  "message": "Dokumen tidak ditemukan",
  "error_code": "DOC_001",
  "requested_id": 9999
}'],
                        ['429', 'Rate Limit Exceeded', '{
  "success": false,
  "message": "Rate limit exceeded",
  "error_code": "RATE_001",
  "limit": 1000,
  "remaining": 0,
  "reset_in": "45 minutes"
}'],
                    ];
                @endphp
                @foreach($errors as [$code, $title, $json])
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-3">
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 font-mono text-sm font-bold text-red-700 ring-1 ring-inset ring-red-200">{{ $code }}</span>
                            <span class="text-sm font-medium text-slate-700">{{ $title }}</span>
                        </div>
                        <div class="p-5">@include('partials.code', ['label' => 'JSON', 'code' => $json])</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- SCHEMA --}}
        <section id="schema" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Struktur Data Lengkap') }}</h2>
            </div>
            <h3 class="mb-2 font-semibold text-slate-800">Document Object (Search Response)</h3>
            @include('partials.code', ['label' => 'JSON', 'code' => '{
  "id": 123,
  "judul": "Peraturan Daerah Kota Kendari Nomor 8 Tahun 2025",
  "nomor_peraturan": "8",
  "tahun_terbit": 2025,
  "tipe_dokumen_nama": "PERATURAN DAERAH KOTA",
  "tipe_dokumen_singkatan": "PERDA",
  "jenis_peraturan": "Peraturan Daerah",
  "status": "Berlaku",
  "tanggal_penetapan": "2025-05-15",
  "tanggal_pengundangan": "2025-05-20",
  "abstrak": "Peraturan ini mengatur tentang...",
  "bidang_hukum": "Ketahanan Pangan",
  "penandatanganan": "Wali Kota Kendari",
  "tempat_penetapan": "Kendari",
  "hit_see": 150,
  "hit_download": 45,
  "created_at": "2025-05-20T10:30:00Z",
  "updated_at": "2025-05-20T10:30:00Z",
  "detail_url": "https://jdih.kendarikota.go.id/api/jdih/documents/123",
  "download_url": "https://jdih.kendarikota.go.id/api/jdih/documents/123/download",
  "gambar_sampul_url": "https://jdih.kendarikota.go.id/storage/covers/123.jpg"
}'])
        </section>

        {{-- TESTING --}}
        <section id="testing" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Testing') }}</h2>
            </div>
            <h3 class="mb-2 font-semibold text-slate-800">{{ __('Script Testing (PowerShell)') }}</h3>
            @include('partials.code', ['label' => 'PowerShell', 'code' => '# test_jdihn_integration.ps1
$API_KEY = "jdih_kendari_jdihn_permanent"
$BASE_URL = "https://jdih.kendarikota.go.id/api/jdih"

Write-Host "JDIH Kota Kendari - JDIHN Integration Test" -ForegroundColor Green
Write-Host "=========================================="

Write-Host "`n1. Testing Health Check..."
Invoke-RestMethod -Uri "$BASE_URL/health" -Method Get

Write-Host "`n2. Testing Search API..."
$searchResult = Invoke-RestMethod -Uri "$BASE_URL/search?q=disabilitas&limit=2" `
    -Method Get -Headers @{"X-API-Key" = $API_KEY}
$searchResult | ConvertTo-Json -Depth 3

Write-Host "`n3. Testing Statistics..."
$stats = Invoke-RestMethod -Uri "$BASE_URL/statistics" `
    -Method Get -Headers @{"X-API-Key" = $API_KEY}
$stats | ConvertTo-Json -Depth 3

Write-Host "`nAll tests completed successfully!" -ForegroundColor Green'])

            <h3 class="mt-6 mb-2 font-semibold text-slate-800">{{ __('Script Testing (Bash/Linux)') }}</h3>
            @include('partials.code', ['label' => 'bash', 'code' => '#!/bin/bash
# test_jdihn_integration.sh

API_KEY="jdih_kendari_jdihn_permanent"
BASE_URL="https://jdih.kendarikota.go.id/api/jdih"

echo "JDIH Kota Kendari - JDIHN Integration Test"
echo "=========================================="

echo -e "\n1. Testing Health Check..."
curl -s "$BASE_URL/health" | jq .

echo -e "\n2. Testing Search API..."
curl -s "$BASE_URL/search?q=disabilitas&limit=2" \
  -H "X-API-Key: $API_KEY" | jq .

echo -e "\n3. Testing Statistics..."
curl -s "$BASE_URL/statistics" \
  -H "X-API-Key: $API_KEY" | jq .

echo -e "\nAll tests completed successfully!"'])
        </section>

        {{-- RATE LIMIT --}}
        <section id="ratelimit" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Rate Limiting') }}</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">JDIHN Permanent Key</div>
                    <div class="mt-1 text-2xl font-bold text-accent">1000 <span class="text-sm font-normal text-slate-400">{{ __('request/jam') }}</span></div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:border-accent/40 hover:shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Testing Key</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">100 <span class="text-sm font-normal text-slate-400">{{ __('request/jam') }}</span></div>
                </div>
            </div>
            <h3 class="mt-5 mb-2 font-semibold text-slate-800">{{ __('Headers untuk monitoring') }}</h3>
            <ul class="space-y-1.5 text-sm">
                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">X-RateLimit-Limit</code> — Maximum requests per hour</li>
                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">X-RateLimit-Remaining</code> — Remaining requests in current window</li>
                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">X-RateLimit-Reset</code> — Unix timestamp when limit resets</li>
                <li><code class="rounded bg-accent/10 px-1.5 py-0.5 font-mono text-accent">Retry-After</code> — Seconds to wait (if limit exceeded)</li>
            </ul>
        </section>

        {{-- POLICY --}}
        <section id="policy" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Kebijakan API Key Permanen') }}</h2>
            </div>
            <ul class="space-y-2 rounded-xl border border-slate-200 bg-white p-5 text-sm">
                <li><strong class="font-semibold text-slate-800">{{ __('Masa berlaku:') }}</strong> {{ __('⭐ PERMANEN — tidak ada tanggal kadaluarsa') }}</li>
                <li><strong class="font-semibold text-slate-800">{{ __('Monitoring:') }}</strong> {{ __('Aktivitas API dipantau 24/7') }}</li>
                <li><strong class="font-semibold text-slate-800">{{ __('Security:') }}</strong> {{ __('Audit keamanan berkala setiap 6 bulan') }}</li>
                <li><strong class="font-semibold text-slate-800">{{ __('Revokasi:') }}</strong> {{ __('Hanya jika ditemukan penyalahgunaan atau atas permintaan resmi') }}</li>
                <li><strong class="font-semibold text-slate-800">{{ __('Notifikasi:') }}</strong> {{ __('Pemberitahuan 30 hari sebelum perubahan kebijakan') }}</li>
                <li><strong class="font-semibold text-slate-800">{{ __('Kontinuitas:') }}</strong> {{ __('Layanan dijamin berkelanjutan untuk integrasi JDIHN') }}</li>
            </ul>
        </section>

        {{-- BEST PRACTICES --}}
        <section id="bestpractices" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Best Practices untuk Integrasi') }}</h2>
            </div>
            <ol class="list-decimal space-y-2 rounded-xl border border-slate-200 bg-white p-5 pl-9 text-sm marker:font-semibold marker:text-accent">
                <li>{!! __('<strong class="font-semibold text-slate-800">Gunakan key permanen</strong> untuk integrasi produksi') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Implement retry logic</strong> dengan exponential backoff') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Cache responses</strong> yang tidak sering berubah (statistik, tipe dokumen)') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Monitor rate limits</strong> via response headers') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Simpan API key</strong> di environment variables, bukan hardcode') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Gunakan pagination</strong> untuk hasil yang banyak') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-800">Error handling</strong> yang baik untuk semua kode error') !!}</li>
            </ol>
        </section>

        {{-- CONTACT --}}
        <section id="contact" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Kontak & Support') }}</h2>
            </div>
            <dl class="grid gap-x-6 gap-y-4 rounded-xl border border-slate-200 bg-white p-5 text-sm sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Institusi') }}</dt><dd class="mt-0.5 font-medium text-slate-800">{{ __('Pemerintah Kota Kendari') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Unit Teknis') }}</dt><dd class="mt-0.5 font-medium text-slate-800">{{ __('Bagian Hukum Setda Kota Kendari') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Email') }}</dt><dd class="mt-0.5 font-medium text-slate-800">admin@jdih.kendari.go.id</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Telepon') }}</dt><dd class="mt-0.5 font-medium text-slate-800">(0401) 3192225</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Website') }}</dt><dd class="mt-0.5 font-medium text-slate-800">https://jdih.kendarikota.go.id</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Jam Support') }}</dt><dd class="mt-0.5 font-medium text-slate-800">{{ __('Senin-Jumat, 08:00-16:00 WITA') }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Alamat') }}</dt><dd class="mt-0.5 font-medium text-slate-800">Jl. Mayjen Sutoyo No. 60, Kendari, Sulawesi Tenggara</dd></div>
            </dl>
        </section>

        {{-- JDIHN NOTES --}}
        <section id="jdihn" class="scroll-mt-8">
            <div class="mb-5 flex items-center gap-3">
                <span class="h-6 w-1 rounded-full bg-primary"></span>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Catatan Penting untuk JDIHN') }}</h2>
            </div>
            <ul class="list-disc space-y-2 rounded-xl border border-accent/20 bg-accent/5 p-5 pl-9 text-sm text-slate-700 marker:text-accent">
                <li>{!! __('API key <strong class="font-semibold text-slate-900">jdih_kendari_jdihn_permanent</strong> bersifat eksklusif untuk JDIHN') !!}</li>
                <li>{!! __('Key ini <strong class="font-semibold text-slate-900">tidak akan kadaluarsa secara otomatis</strong>') !!}</li>
                <li>{!! __('Rate limit: <strong class="font-semibold text-slate-900">1000 request/jam</strong> (dapat ditingkatkan jika diperlukan)') !!}</li>
                <li>{!! __('Semua data dijamin <strong class="font-semibold text-slate-900">up-to-date</strong> dengan database utama') !!}</li>
                <li>{!! __('Dokumen baru akan tersedia di API maksimal <strong class="font-semibold text-slate-900">1 jam</strong> setelah diunggah') !!}</li>
                <li>{!! __('Maintenance dijadwalkan <strong class="font-semibold text-slate-900">akhir pekan</strong> dengan pemberitahuan 72 jam') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-900">SLA Uptime:</strong> 99.5% bulanan') !!}</li>
                <li>{!! __('<strong class="font-semibold text-slate-900">Support Response:</strong> Maksimal 4 jam untuk isu kritis') !!}</li>
            </ul>
        </section>

        {{-- FOOTER --}}
        <footer class="border-t border-slate-200 pt-6 text-sm text-slate-500">
            <p class="font-semibold text-slate-700">{{ __('Dokumentasi API JDIH Kota Kendari v1.0') }}</p>
            <p class="mt-1">{{ __('© 2026 Pemerintah Kota Kendari. Hak cipta dilindungi undang-undang.') }}</p>
            <p>{{ __('Versi Dokumen: 1.0.1 · Terakhir diperbarui: 18 Januari 2026') }}</p>
            <p>{!! __('API Key Permanen untuk: <strong class="font-semibold text-slate-700">Integrasi Permanen JDIHN Nasional</strong>') !!}</p>
        </footer>

    </main>
</div>

<script>
    // Copy-to-clipboard untuk setiap blok kode
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const code = btn.closest('[data-code-block]').querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(function () {
                const prev = btn.textContent;
                btn.textContent = @json(__('Tersalin!'));
                setTimeout(function () { btn.textContent = prev; }, 1500);
            });
        });
    });

    // Scroll-spy: tandai item sidebar sesuai section yang sedang dibaca (realtime)
    (function () {
        const links = Array.from(document.querySelectorAll('.nav-link'));
        if (!links.length) return;
        const order = links.map(l => l.dataset.nav);
        const map = Object.fromEntries(links.map(l => [l.dataset.nav, l]));
        const visible = new Set();

        function setActive(id) {
            links.forEach(l => l.classList.toggle('active', l.dataset.nav === id));
        }

        const io = new IntersectionObserver(function (entries) {
            entries.forEach(e => e.isIntersecting ? visible.add(e.target.id) : visible.delete(e.target.id));
            // section teratas yang terlihat = yang aktif
            const active = order.find(id => visible.has(id));
            if (active) setActive(active);
        }, { rootMargin: '0px 0px -75% 0px', threshold: 0 });

        order.forEach(id => {
            const sec = document.getElementById(id);
            if (sec) io.observe(sec);
        });
    })();
</script>
</body>
</html>
