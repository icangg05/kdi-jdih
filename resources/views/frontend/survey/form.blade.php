<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Survei Kepuasan Pengguna') }} — JDIH Kota Kendari</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Font sama dengan situs: Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome sama versi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />

    <!-- Tailwind + token brand (compiled) -->
    @vite('resources/css/app.css')

    <style>
        /* Latar foto Kendari + scrim, dipasang fixed supaya kartu terasa mengambang */
        .survey-bg {
            position: fixed;
            inset: 0;
            background: url('{{ asset('assets/img/background.webp') }}') center/cover no-repeat;
        }

        .survey-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2, 6, 23, .82) 0%, rgba(2, 6, 23, .88) 45%, rgba(2, 6, 23, .95) 100%);
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

        /* Kartu penilaian: bintang/emoji terpilih */
        .rating-option:has(input:checked) {
            border-color: var(--color-primary, #ff891e);
            background: color-mix(in srgb, var(--color-primary, #ff891e) 12%, white);
            color: var(--color-primary, #ff891e);
        }

        .rating-option:has(input:focus-visible) {
            outline: 2px solid var(--color-primary, #ff891e);
            outline-offset: 2px;
        }
    </style>
</head>

<body class="font-opensans antialiased bg-darkbg text-slate-700">

    <!-- LATAR -->
    <div aria-hidden="true" class="survey-bg"></div>
    <div aria-hidden="true" class="survey-grid"></div>
    <div aria-hidden="true" class="survey-glow -top-32 -left-24 h-96 w-96 bg-accent/25"></div>
    <div aria-hidden="true" class="survey-glow top-1/3 -right-32 h-96 w-96 bg-primary/20"></div>

    <div class="relative px-4 py-8 lg:py-14">
        <div class="mx-auto max-w-3xl">

            <!-- NAV ATAS -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-3.5 py-2 text-sm font-medium text-white/80 backdrop-blur-sm transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Kembali ke Beranda') }}
                </a>
                <img src="{{ asset('assets/img/logo-new-jdih.png') }}" alt="Logo JDIH Kota Kendari" class="h-10 w-auto drop-shadow">
            </div>

            <!-- HERO -->
            <header class="animate-rise mb-8 text-center">
                <span class="inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-200 backdrop-blur-sm">
                    <span class="h-1.5 w-1.5 rounded bg-primary"></span>
                    {{ __('Suara Anda, Layanan Kami') }}
                </span>

                <h1 class="mt-5 text-3xl md:text-4xl font-bold leading-tight tracking-tight text-white text-balance">
                    {{ __('Survei Kepuasan Pengguna') }}
                </h1>
                <p class="mx-auto mt-3 max-w-xl text-sm lg:text-base text-slate-300">
                    {{ __('Lima menit dari Anda menentukan arah perbaikan JDIH Kota Kendari — dari kelengkapan dokumen sampai kecepatan pencarian.') }}
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-300">
                    @foreach ([
                        ['icon' => 'fa-clock', 'text' => __('±5 menit')],
                        ['icon' => 'fa-shield-halved', 'text' => __('Data dijaga kerahasiaannya')],
                        ['icon' => 'fa-list-check', 'text' => __('4 bagian singkat')],
                    ] as $meta)
                        <span class="inline-flex items-center gap-2 rounded border border-white/10 bg-white/5 px-3 py-1.5 backdrop-blur-sm">
                            <i class="fas {{ $meta['icon'] }} text-primary"></i>
                            {{ $meta['text'] }}
                        </span>
                    @endforeach
                </div>
            </header>

            <!-- KARTU FORM -->
            <div class="animate-rise relative overflow-hidden rounded bg-white shadow-2xl shadow-black/40 ring-1 ring-white/10" style="animation-delay: .08s">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary to-accent"></span>

                <div class="p-5 sm:p-8 lg:p-10">
                    @if ($errors->any())
                        <div class="mb-8 flex items-start gap-3 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <i class="fas fa-circle-exclamation mt-0.5"></i>
                            <div>
                                <p class="font-semibold">{{ __('Ada isian yang perlu diperbaiki') }}</p>
                                <p class="mt-0.5 text-red-600/80">{{ __('Silakan cek kembali kolom yang ditandai merah di bawah.') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('survey.store') }}" method="POST" class="space-y-10">
                        @csrf

                        <!-- 1. INFORMASI PENGGUNA -->
                        <section>
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 flex-none items-center justify-center rounded bg-primary text-sm font-bold text-white">1</span>
                                <div>
                                    <h2 class="text-lg font-bold leading-tight text-slate-900">{{ __('Informasi Pengguna') }}</h2>
                                    <p class="text-xs text-slate-500">{{ __('Agar kami tahu siapa yang kami layani') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        {{ __('Nama Lengkap') }} <span class="text-primary">*</span>
                                    </label>
                                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                                        placeholder="{{ __('Contoh: Andi Saputra') }}"
                                        class="w-full rounded border {{ $errors->has('nama') ? 'border-red-400' : 'border-slate-300' }} bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">
                                    @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="{{ __('nama@email.com') }}"
                                        class="w-full rounded border {{ $errors->has('email') ? 'border-red-400' : 'border-slate-300' }} bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">
                                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="instansi" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Instansi/Perusahaan') }}</label>
                                    <input type="text" id="instansi" name="instansi" value="{{ old('instansi') }}"
                                        placeholder="{{ __('Contoh: Universitas Halu Oleo') }}"
                                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">
                                </div>

                                <div>
                                    <label for="jenis_pengguna" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                        {{ __('Jenis Pengguna') }} <span class="text-primary">*</span>
                                    </label>
                                    <select id="jenis_pengguna" name="jenis_pengguna" required
                                        class="w-full rounded border {{ $errors->has('jenis_pengguna') ? 'border-red-400' : 'border-slate-300' }} bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/30">
                                        <option value="">{{ __('Pilih Jenis Pengguna') }}</option>
                                        <option value="Mahasiswa" @selected(old('jenis_pengguna') == 'Mahasiswa')>{{ __('Mahasiswa') }}</option>
                                        <option value="Akademisi" @selected(old('jenis_pengguna') == 'Akademisi')>{{ __('Akademisi') }}</option>
                                        <option value="Praktisi Hukum" @selected(old('jenis_pengguna') == 'Praktisi Hukum')>{{ __('Praktisi Hukum') }}</option>
                                        <option value="Masyarakat Umum" @selected(old('jenis_pengguna') == 'Masyarakat Umum')>{{ __('Masyarakat Umum') }}</option>
                                        <option value="Lainnya" @selected(old('jenis_pengguna') == 'Lainnya')>{{ __('Lainnya') }}</option>
                                    </select>
                                    @error('jenis_pengguna') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </section>

                        <!-- 2. PENILAIAN LAYANAN -->
                        <section>
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 flex-none items-center justify-center rounded bg-primary text-sm font-bold text-white">2</span>
                                <div>
                                    <h2 class="text-lg font-bold leading-tight text-slate-900">{{ __('Penilaian Layanan') }}</h2>
                                    <p class="text-xs text-slate-500">{{ __('Pilih satu wajah: 1 sangat tidak puas, 5 sangat puas') }}</p>
                                </div>
                            </div>

                            @php
                                $skala = [
                                    1 => ['icon' => 'fa-face-angry', 'label' => __('Sangat Tidak Puas')],
                                    2 => ['icon' => 'fa-face-frown', 'label' => __('Tidak Puas')],
                                    3 => ['icon' => 'fa-face-meh', 'label' => __('Cukup')],
                                    4 => ['icon' => 'fa-face-smile', 'label' => __('Puas')],
                                    5 => ['icon' => 'fa-face-grin-stars', 'label' => __('Sangat Puas')],
                                ];
                            @endphp

                            <div class="space-y-4">
                                @foreach ([
                                    ['id' => 'kemudahan_akses', 'icon' => 'fa-hand-pointer', 'label' => 'Kemudahan Akses', 'desc' => 'Kemudahan dalam mengakses informasi dan dokumen hukum'],
                                    ['id' => 'kelengkapan_informasi', 'icon' => 'fa-folder-open', 'label' => 'Kelengkapan Informasi', 'desc' => 'Kelengkapan dokumen dan informasi yang disediakan'],
                                    ['id' => 'kecepatan_loading', 'icon' => 'fa-gauge-high', 'label' => 'Kecepatan Loading', 'desc' => 'Kecepatan akses dan loading halaman website'],
                                    ['id' => 'tampilan_antarmuka', 'icon' => 'fa-palette', 'label' => 'Tampilan Antarmuka', 'desc' => 'Tampilan dan antarmuka yang mudah digunakan'],
                                    ['id' => 'relevansi_pencarian', 'icon' => 'fa-magnifying-glass', 'label' => 'Relevansi Pencarian', 'desc' => 'Kesesuaian hasil pencarian dengan kata kunci'],
                                ] as $index => $item)
                                    <fieldset class="rounded border {{ $errors->has($item['id']) ? 'border-red-400' : 'border-slate-200' }} bg-slate-50/70 p-4 transition hover:border-slate-300">
                                        <legend class="sr-only">{{ __($item['label']) }}</legend>

                                        <div class="mb-3 flex items-start gap-3">
                                            <span class="flex h-8 w-8 flex-none items-center justify-center rounded bg-white text-accent ring-1 ring-slate-200">
                                                <i class="fas {{ $item['icon'] }} text-xs"></i>
                                            </span>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    <span class="text-slate-400 tabular-nums">{{ $index + 1 }}.</span> {{ __($item['label']) }}
                                                    <span class="text-primary">*</span>
                                                </p>
                                                <p class="text-xs text-slate-500">{{ __($item['desc']) }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
                                            @foreach ($skala as $nilai => $s)
                                                <label class="rating-option flex cursor-pointer flex-col items-center gap-1 rounded border border-slate-200 bg-white px-1 py-2.5 text-slate-400 transition hover:border-primary/50 hover:text-primary/70">
                                                    <input type="radio" name="{{ $item['id'] }}" id="{{ $item['id'] }}_{{ $nilai }}" value="{{ $nilai }}" required @checked(old($item['id']) == $nilai) class="sr-only">
                                                    <i class="fas {{ $s['icon'] }} text-xl"></i>
                                                    <span class="text-[10px] font-semibold leading-tight text-center sm:text-[11px]">{{ $s['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        @error($item['id']) <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </fieldset>
                                @endforeach
                            </div>
                        </section>

                        <!-- 3. SARAN DAN HARAPAN -->
                        <section>
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 flex-none items-center justify-center rounded bg-primary text-sm font-bold text-white">3</span>
                                <div>
                                    <h2 class="text-lg font-bold leading-tight text-slate-900">{{ __('Saran dan Harapan') }}</h2>
                                    <p class="text-xs text-slate-500">{{ __('Bagian ini opsional, tapi paling kami tunggu') }}</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label for="saran_perbaikan" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Saran Perbaikan') }}</label>
                                    <textarea id="saran_perbaikan" name="saran_perbaikan" rows="3" maxlength="1000"
                                        placeholder="{{ __('Contoh: dokumen tahun 2015 ke bawah masih sulit ditemukan') }}"
                                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">{{ old('saran_perbaikan') }}</textarea>
                                </div>

                                <div>
                                    <label for="fitur_harapan" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Fitur yang Diharapkan') }}</label>
                                    <textarea id="fitur_harapan" name="fitur_harapan" rows="3" maxlength="1000"
                                        placeholder="{{ __('Contoh: notifikasi bila ada peraturan baru yang terbit') }}"
                                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">{{ old('fitur_harapan') }}</textarea>
                                </div>
                            </div>
                        </section>

                        <!-- 4. KONTAK TAMBAHAN -->
                        <section>
                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-9 w-9 flex-none items-center justify-center rounded bg-primary text-sm font-bold text-white">4</span>
                                <div>
                                    <h2 class="text-lg font-bold leading-tight text-slate-900">{{ __('Kontak Tambahan') }}</h2>
                                    <p class="text-xs text-slate-500">{{ __('Opsional — hanya bila Anda ingin kami hubungi') }}</p>
                                </div>
                            </div>

                            <label class="flex cursor-pointer items-start gap-3 rounded border border-slate-200 bg-slate-50/70 p-4 transition hover:border-primary/40">
                                <input type="checkbox" id="bersedia_dihubungi" name="bersedia_dihubungi" value="1" @checked(old('bersedia_dihubungi'))
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 accent-primary focus:ring-primary/40">
                                <span class="text-sm text-slate-700">{{ __('Saya bersedia dihubungi untuk informasi lebih lanjut') }}</span>
                            </label>

                            <div class="mt-5">
                                <label for="kontak" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Kontak (WhatsApp/Telepon)') }}</label>
                                <input type="text" id="kontak" name="kontak" value="{{ old('kontak') }}" inputmode="tel"
                                    placeholder="{{ __('Contoh: 0812-3456-7890') }}"
                                    class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/30">
                            </div>
                        </section>

                        <!-- SUBMIT -->
                        <div class="border-t border-slate-200 pt-6">
                            <button type="submit"
                                class="flex w-full items-center justify-center gap-3 rounded bg-primary px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2">
                                <i class="fas fa-paper-plane"></i>
                                {{ __('Kirim Survei') }}
                            </button>
                            <p class="mt-3 flex items-center justify-center gap-2 text-xs text-slate-400">
                                <i class="fas fa-lock"></i>
                                {{ __('Jawaban Anda hanya digunakan untuk evaluasi layanan JDIH.') }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-white/40">
                &copy; {{ date('Y') }} JDIH Kota Kendari
            </p>
        </div>
    </div>
</body>
</html>
