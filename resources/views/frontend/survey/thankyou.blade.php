<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Terima Kasih') }} — Survei JDIH Kota Kendari</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Font sama dengan situs: Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />

    @vite('resources/css/app.css')

    <style>
        .survey-bg {
            position: fixed;
            inset: 0;
            background: url('{{ asset('assets/img/background.webp') }}') center/cover no-repeat;
        }

        .survey-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2, 6, 23, .82) 0%, rgba(2, 6, 23, .9) 50%, rgba(2, 6, 23, .96) 100%);
        }

        .survey-grid {
            position: fixed;
            inset: 0;
            opacity: .12;
            background-image: radial-gradient(rgba(255, 255, 255, .7) 1px, transparent 1px);
            background-size: 32px 32px;
            -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black, transparent 75%);
            mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black, transparent 75%);
        }

        .survey-glow {
            position: fixed;
            border-radius: 4px;
            filter: blur(90px);
        }

        /* Denyut halus di lingkaran centang */
        @keyframes pulseRing {
            0% { transform: scale(.92); opacity: .55 }
            70% { transform: scale(1.25); opacity: 0 }
            100% { transform: scale(1.25); opacity: 0 }
        }

        .check-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 4px;
            background: var(--color-primary, #ff891e);
            animation: pulseRing 2.4s cubic-bezier(.4, 0, .6, 1) infinite;
        }

        /* Bar penilaian terisi dari nol */
        @keyframes growBar {
            from { transform: scaleX(0) }
            to { transform: scaleX(1) }
        }

        .bar-fill {
            transform-origin: left;
            animation: growBar .9s cubic-bezier(.22, 1, .36, 1) both;
        }

        @media (prefers-reduced-motion: reduce) {
            .check-ring::before, .bar-fill { animation: none }
        }
    </style>
</head>

<body class="font-opensans antialiased bg-darkbg text-slate-700">

    <!-- LATAR -->
    <div aria-hidden="true" class="survey-bg"></div>
    <div aria-hidden="true" class="survey-grid"></div>
    <div aria-hidden="true" class="survey-glow -top-32 -left-24 h-96 w-96 bg-primary/25"></div>
    <div aria-hidden="true" class="survey-glow bottom-0 -right-32 h-96 w-96 bg-accent/25"></div>

    <div class="relative flex min-h-dvh items-center justify-center px-4 py-12">
        <div class="w-full max-w-xl space-y-5">

            <!-- KARTU SUKSES -->
            <div class="animate-rise relative overflow-hidden rounded bg-white text-center shadow-2xl shadow-black/40 ring-1 ring-white/10">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary to-accent"></span>

                <div class="p-8">
                    <div class="relative mx-auto mb-6 h-20 w-20">
                        <span aria-hidden="true" class="check-ring absolute inset-0 rounded"></span>
                        <span class="relative flex h-20 w-20 items-center justify-center rounded bg-primary text-white shadow-lg shadow-primary/30">
                            <i class="fas fa-check text-3xl"></i>
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">
                        {{ __('Terima Kasih') }}
                    </h1>
                    <p class="mx-auto mt-3 max-w-md text-sm lg:text-base text-slate-600">
                        {{ __('Survei Anda sudah kami terima. Masukan ini langsung masuk ke bahan evaluasi layanan JDIH Kota Kendari.') }}
                    </p>

                    @if (session('success'))
                        <div class="mx-auto mt-5 inline-flex items-center gap-2 rounded bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
                            <i class="fas fa-check"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                <!-- RINGKASAN ANGKA -->
                <div class="grid grid-cols-2 divide-x divide-slate-200 border-t border-slate-200 bg-slate-50">
                    <div class="p-5">
                        <div class="text-3xl font-bold tabular-nums text-accent">{{ $total_survei ?? 0 }}</div>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Total Responden') }}</p>
                    </div>
                    <div class="p-5">
                        <div class="text-3xl font-bold tabular-nums text-primary">{{ $average_rating ?? 0 }}<span class="text-lg text-slate-400">/5</span></div>
                        <div class="mt-1.5 flex justify-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= floor($average_rating ?? 0) ? 'text-primary' : 'text-slate-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL PENILAIAN -->
            <div class="animate-rise rounded bg-white p-6 shadow-2xl shadow-black/40 ring-1 ring-white/10" style="animation-delay: .08s">
                <h2 class="mb-5 flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-700">
                    <i class="fas fa-chart-simple text-primary"></i>
                    {{ __('Penilaian Rata-rata Pengguna') }}
                </h2>

                <div class="space-y-3.5">
                    @foreach ([
                        ['label' => __('Kemudahan Akses'), 'icon' => 'fa-hand-pointer', 'value' => $average_kemudahan ?? 0],
                        ['label' => __('Kelengkapan Info'), 'icon' => 'fa-folder-open', 'value' => $average_kelengkapan ?? 0],
                        ['label' => __('Kecepatan Loading'), 'icon' => 'fa-gauge-high', 'value' => $average_kecepatan ?? 0],
                        ['label' => __('Tampilan Website'), 'icon' => 'fa-palette', 'value' => $average_tampilan ?? 0],
                        ['label' => __('Relevansi Hasil'), 'icon' => 'fa-magnifying-glass', 'value' => $average_relevansi ?? 0],
                    ] as $i => $item)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 font-medium text-slate-600">
                                    <i class="fas {{ $item['icon'] }} w-4 text-center text-xs text-slate-400"></i>
                                    {{ $item['label'] }}
                                </span>
                                <span class="font-bold tabular-nums text-slate-800">{{ $item['value'] }}<span class="text-slate-400">/5</span></span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded bg-slate-200">
                                <div class="bar-fill h-full rounded bg-linear-to-r from-primary to-accent"
                                     style="width: {{ ($item['value'] / 5) * 100 }}%; animation-delay: {{ $i * 0.08 }}s"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- AKSI -->
            <div class="animate-rise grid gap-3 sm:grid-cols-2" style="animation-delay: .16s">
                <a href="{{ url('/') }}"
                    class="flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
                    <i class="fas fa-house"></i>
                    {{ __('Kembali ke Beranda') }}
                </a>

                <a href="{{ route('survey.create') }}"
                    class="flex items-center justify-center gap-2.5 rounded border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white/85 backdrop-blur-sm transition hover:bg-white/10 hover:text-white active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <i class="fas fa-pen-to-square"></i>
                    {{ __('Isi Survei Lagi') }}
                </a>
            </div>

            <!-- COUNTDOWN + FOOTER -->
            <div class="text-center">
                <p class="inline-flex items-center gap-2 rounded border border-white/10 bg-white/5 px-4 py-2 text-xs text-slate-300 backdrop-blur-sm">
                    <i class="fas fa-arrow-rotate-right text-primary"></i>
                    {{ __('Kembali ke beranda otomatis dalam') }}
                    <span id="countdown" class="font-bold tabular-nums text-primary">15</span>
                    {{ __('detik') }}
                    <button type="button" id="batalRedirect" class="ml-1 font-semibold text-white/70 underline underline-offset-2 transition hover:text-white">
                        {{ __('Batalkan') }}
                    </button>
                </p>
                <p class="mt-4 text-xs text-white/40">
                    &copy; {{ date('Y') }} {{ __('Dinas Komunikasi dan Informatika Kota Kendari') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Countdown & redirect otomatis ke beranda (bisa dibatalkan)
        (function () {
            var sisa = 15;
            var el = document.getElementById('countdown');
            var batal = document.getElementById('batalRedirect');

            var timer = setInterval(function () {
                sisa -= 1;
                if (el) el.textContent = sisa;
                if (sisa <= 0) {
                    clearInterval(timer);
                    window.location.href = @js(url('/'));
                }
            }, 1000);

            batal?.addEventListener('click', function () {
                clearInterval(timer);
                batal.closest('p').remove();
            });
        })();
    </script>
</body>
</html>
