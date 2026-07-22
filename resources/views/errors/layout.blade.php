{{--
    Kerangka halaman error.

    Sengaja berdiri sendiri (tanpa layout frontend / Livewire / query DB) supaya
    tetap tampil saat penyebab errornya justru ada di lapisan itu — mis. 500.

    Slot yang dipakai turunan:
      @section('kode')     kode status, mis. 404
      @section('judul')    judul singkat
      @section('pesan')    satu-dua kalimat penjelasan
      @section('ikon')     kelas Font Awesome
      @section('aksi')     tombol utama (opsional, ada default)
      @section('bantuan')  blok tambahan seperti pencarian / catatan (opsional)
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('kode') — @yield('judul') | JDIH Kota Kendari</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />

    @vite('resources/css/app.css')

    <style>
        .err-bg {
            position: fixed;
            inset: 0;
            background: url('{{ asset('assets/img/background.webp') }}') center/cover no-repeat;
        }

        .err-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2, 6, 23, .84) 0%, rgba(2, 6, 23, .9) 50%, rgba(2, 6, 23, .96) 100%);
        }

        .err-grid {
            position: fixed;
            inset: 0;
            opacity: .12;
            background-image: radial-gradient(rgba(255, 255, 255, .7) 1px, transparent 1px);
            background-size: 32px 32px;
            -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black, transparent 75%);
            mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black, transparent 75%);
        }

        .err-glow {
            position: fixed;
            border-radius: 4px;
            filter: blur(90px);
        }

        /* Kode status: besar tapi tidak berteriak — tetap di bawah pagu display */
        .err-kode {
            font-size: clamp(3.5rem, 14vw, 5.5rem);
            font-weight: 700;
            line-height: .95;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
        }
    </style>
    @stack('styles')
</head>

<body class="font-opensans antialiased bg-darkbg text-slate-300">

    <div aria-hidden="true" class="err-bg"></div>
    <div aria-hidden="true" class="err-grid"></div>
    <div aria-hidden="true" class="err-glow -top-32 -left-24 h-96 w-96 bg-accent/25"></div>
    <div aria-hidden="true" class="err-glow bottom-0 -right-32 h-96 w-96 bg-primary/20"></div>

    <div class="relative flex min-h-dvh flex-col px-4 py-6 sm:py-10">

        {{-- NAV ATAS --}}
        <div class="mx-auto flex w-full max-w-3xl items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-3 rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60">
                <img src="{{ asset('assets/img/logo-new-jdih.png') }}" alt="" class="h-10 w-auto drop-shadow">
                <span class="hidden text-sm font-semibold text-white/85 sm:block">JDIH Kota Kendari</span>
            </a>
            <span class="rounded border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-white/60 backdrop-blur-sm">
                {{ __('Kode') }} @yield('kode')
            </span>
        </div>

        {{-- ISI --}}
        <main class="mx-auto flex w-full max-w-3xl flex-1 flex-col justify-center py-10">

            <div class="animate-rise">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded bg-primary/15 text-primary ring-1 ring-primary/30">
                    <i class="fas @yield('ikon', 'fa-triangle-exclamation') text-lg"></i>
                </span>

                <p class="err-kode mt-5 text-white">@yield('kode')</p>

                <h1 class="mt-3 text-2xl font-bold tracking-tight text-white text-balance sm:text-3xl">
                    @yield('judul')
                </h1>

                <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-300 sm:text-base">
                    @yield('pesan')
                </p>
            </div>

            {{-- AKSI --}}
            <div class="animate-rise mt-7 flex flex-col gap-3 sm:flex-row" style="animation-delay: .06s">
                @hasSection('aksi')
                    @yield('aksi')
                @else
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
                        <i class="fas fa-house"></i>
                        {{ __('Kembali ke Beranda') }}
                    </a>
                @endif

                {{-- Disembunyikan bila tab ini tidak punya riwayat — tombol yang tak melakukan apa-apa lebih buruk daripada tidak ada --}}
                <button type="button" id="tombolKembali" hidden onclick="history.back()"
                        class="inline-flex items-center justify-center gap-2.5 rounded border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white/85 backdrop-blur-sm transition hover:bg-white/10 hover:text-white active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Halaman Sebelumnya') }}
                </button>
                <script>
                    if (history.length > 1) document.getElementById('tombolKembali').hidden = false;
                </script>
            </div>

            @hasSection('bantuan')
                <div class="animate-rise mt-8" style="animation-delay: .12s">
                    @yield('bantuan')
                </div>
            @endif

            {{-- JALAN PINTAS: inti situs ini adalah temu-balik dokumen --}}
            <div class="animate-rise mt-10 border-t border-white/10 pt-6" style="animation-delay: .18s">
                <p class="text-xs font-semibold uppercase tracking-wider text-white/50">{{ __('Cari dokumen di kategori') }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ([
                        ['kategori' => 'peraturan', 'label' => __('Peraturan'), 'icon' => 'fa-scale-balanced'],
                        ['kategori' => 'monografi', 'label' => __('Monografi'), 'icon' => 'fa-book'],
                        ['kategori' => 'artikel', 'label' => __('Artikel'), 'icon' => 'fa-newspaper'],
                        ['kategori' => 'putusan', 'label' => __('Putusan'), 'icon' => 'fa-gavel'],
                    ] as $tautan)
                        <a href="{{ url(app()->getLocale() . '/dokumen/' . $tautan['kategori']) }}"
                           class="inline-flex items-center gap-2 rounded border border-white/10 bg-white/5 px-3.5 py-2 text-sm text-white/80 transition hover:border-primary/40 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                            <i class="fas {{ $tautan['icon'] }} text-xs text-primary"></i>
                            {{ $tautan['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </main>

        {{-- FOOTER --}}
        <footer class="mx-auto w-full max-w-3xl border-t border-white/10 pt-5 text-xs text-white/40">
            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} JDIH Kota Kendari</p>
                <p>
                    {{ __('Masalah berlanjut?') }}
                    <a href="mailto:jdih@kendarikota.go.id" class="font-semibold text-white/70 underline underline-offset-2 transition hover:text-primary">
                        jdih@kendarikota.go.id
                    </a>
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
