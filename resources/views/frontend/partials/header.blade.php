@php
    $menus = config('app.menus');

    // ambil jenis informasi hukum dari database
    $jenisInformasiHukum = DB::table('jenis_informasi_hukum')
        ->select('id', 'singkatan')
        ->orderBy('singkatan')
        ->get()
        ->map(
            fn($row) => [
                'label' => $row->singkatan,
                'route' => 'frontend.informasi-hukum.index',
                'param' => ['id' => Hashids::encode($row->id)],
            ],
        )
        ->toArray();

    // PERUBAHAN: Hapus sub-menu dari Pembentukan PUU
    foreach ($menus as &$menu) {
        if ($menu['label'] === 'Informasi Hukum') {
            // Jika sudah ada sub menu, gabungkan dengan data dari database
            if (isset($menu['sub']) && is_array($menu['sub'])) {
                // CARI menu Pembentukan PUU dan hapus sub-menu-nya
                foreach ($menu['sub'] as &$subItem) {
                    // Cek apakah ini menu Pembentukan PUU
                    if (isset($subItem['label']) && $subItem['label'] === 'Pembentukan PUU') {
                        // HAPUS sub-menu Pembentukan PUU (Naskah Akademik, Rancangan PUU, dll)
                        if (isset($subItem['sub'])) {
                            unset($subItem['sub']);
                        }
                        // Pastikan menu Pembentukan PUU langsung mengarah ke route-nya
                        if (!isset($subItem['route'])) {
                            $subItem['route'] = 'frontend.pembentukan-puu.index';
                        }
                    }
                }
                unset($subItem);

                // Gabungkan dengan data database
                $menu['sub'] = array_merge($menu['sub'], $jenisInformasiHukum);
            } else {
                $menu['sub'] = $jenisInformasiHukum;
            }
        }
    }
    unset($menu);

    // Path tanpa prefix locale ({locale} = id|en|zh|ko). request()->is('berita')
    // selalu false karena path sebenarnya 'id/berita' — itu sebabnya hanya
    // Beranda (yang memakai routeIs) yang pernah tampil aktif.
    $segments = request()->segments();
    if (in_array($segments[0] ?? '', ['id', 'en', 'zh', 'ko'], true)) {
        array_shift($segments);
    }
    $currentPath = implode('/', $segments);

    // startActive boleh string atau array (satu menu bisa punya beberapa prefix URL)
    $isMenuActive = function ($menu) use ($currentPath) {
        if (isset($menu['startActive'])) {
            foreach ((array) $menu['startActive'] as $prefix) {
                if ($currentPath === $prefix || str_starts_with($currentPath, $prefix . '/')) {
                    return true;
                }
            }
            return false;
        }

        return isset($menu['route']) ? request()->routeIs($menu['route'] . '*') : false;
    };

    $currentUrl = request()->url();
@endphp

<header
    class="w-full fixed top-0 z-50 text-white
        bg-darkbg/80 backdrop-blur-md
        border-b border-white/8
        shadow-[0_8px_30px_-16px_rgba(2,6,23,0.8)]"
    x-data="{ mobileOpen: false }">
    <div class="max-w-400 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-8 h-14 lg:h-16">

            <!-- BRAND LOCKUP -->
            <div class="flex items-center gap-3">
                <!-- Logo JDIH + Pemkot -->
                <a wire:navigate href="{{ route('frontend.beranda') }}"
                    aria-label="{{ __('Beranda') }} JDIH Kota Kendari"
                    class="flex items-center gap-2 shrink-0 rounded-md transition-opacity duration-200 hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
                    <img src="{{ asset('assets/img/jdihnn.png') }}" alt="JDIH" class="h-9 lg:h-11">
                    <img src="{{ asset('assets/img/kendari.png') }}" alt="Pemkot Kendari" class="h-8 lg:h-10">
                </a>

                <!-- Ornamen: garis pemisah vertikal -->
                <span aria-hidden="true" class="hidden sm:block h-7 lg:h-9 w-px bg-linear-to-b from-transparent via-white/25 to-transparent"></span>

                <!-- Wordmark -->
                <a wire:navigate href="{{ route('frontend.beranda') }}"
                    class="leading-snug rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
                    <p class="text-[10px] sm:text-[11px] font-semibold tracking-widest uppercase text-white/80 whitespace-nowrap">
                        <span class="hidden sm:inline">{{ __('Jaringan Dokumentasi dan Informasi Hukum') }}</span>
                        <span class="inline sm:hidden">JDIH</span>
                    </p>
                    <p class="text-[11px] sm:text-xs font-bold tracking-[0.06em] uppercase text-primary whitespace-nowrap">
                        {{ __('Pemerintah Kota Kendari') }}
                    </p>
                </a>
            </div>

            <!-- DESKTOP MENU - VERSI SIMPLE -->
            <nav class="hidden min-[1360px]:flex items-center gap-5 min-[1500px]:gap-7 text-[12px] font-normal tracking-normal">

                @foreach ($menus as $menu)
                    @php
                        $parentActive = $isMenuActive($menu);
                    @endphp

                    {{-- MENU TANPA SUB --}}
                    @if (!isset($menu['sub']) || empty($menu['sub']))
                        <a wire:navigate.hover
                            href="{{ isset($menu['route']) ? route($menu['route']) : '#' }}"
                            class="relative uppercase whitespace-nowrap transition-colors duration-200 rounded-sm
                               after:absolute after:inset-x-0 after:-bottom-1.5 after:h-[2px] after:origin-left after:bg-primary after:transition-transform after:duration-300 after:ease-out
                               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-4 focus-visible:ring-offset-darkbg
                               {{ $parentActive ? 'text-primary after:scale-x-100' : 'text-white/75 hover:text-primary after:scale-x-0 hover:after:scale-x-100' }}">
                            {{ __($menu['label']) }}
                        </a>

                    {{-- MENU DENGAN SUB --}}
                    @else
                        <div class="relative"
                            x-data="{ open: false, flip: false, active: {{ $parentActive ? 'true' : 'false' }} }"
                            {{-- ruang sisa di kanan trigger < lebar panel (min-w-56 = 224px) + 16px margin --}}
                            @mouseenter="flip = window.innerWidth - $el.getBoundingClientRect().left < 240; open = true"
                            @mouseleave="open = false">

                            <button type="button"
                                @click.prevent
                                class="relative flex items-center gap-1 uppercase whitespace-nowrap select-none cursor-default transition-colors duration-200 rounded-sm
                                    after:absolute after:inset-x-0 after:-bottom-1.5 after:h-[2px] after:origin-left after:bg-primary after:transition-transform after:duration-300 after:ease-out
                                    focus:outline-none"
                                :class="(open || active) ? 'text-primary after:scale-x-100' : 'text-white/75 after:scale-x-0'">
                                {{ __($menu['label']) }}
                                <svg class="w-4 h-4 transition-transform duration-300"
                                    :class="open && 'rotate-180'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- SUBMENU LEVEL 2 -->
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                :class="flip ? 'right-0' : 'left-0'"
                                class="absolute top-full pt-3.5 z-50">

                                <div
                                    class="min-w-56 rounded-xl
                                        bg-darkbg/95 backdrop-blur-md
                                        shadow-2xl shadow-black/40 ring-1 ring-white/10
                                        overflow-hidden">

                                    @foreach ($menu['sub'] as $sub)
                                        @php
                                            $subUrl = isset($sub['param']) ? route($sub['route'], $sub['param']) : route($sub['route']);
                                            $subActive = $subUrl === $currentUrl;
                                        @endphp
                                        <a wire:navigate.hover
                                            href="{{ $subUrl }}"
                                            @if ($subActive) aria-current="page" @endif
                                            class="block px-4.5 py-2.5 text-sm transition-colors duration-200
                                               focus-visible:outline-none focus-visible:bg-white/[0.06] focus-visible:text-primary
                                               {{ $subActive ? 'bg-white/[0.06] text-primary font-semibold' : 'text-white/70 hover:text-primary hover:bg-white/[0.06]' }}
                                               {{ $loop->first ? 'pt-4' : '' }}
                                               {{ $loop->last ? 'pb-4' : '' }}
                                               border-b border-white/5 last:border-b-0">
                                            {{ __($sub['label']) }}
                                        </a>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- MOBILE BUTTON -->
            <button @click="mobileOpen = !mobileOpen"
                aria-label="{{ __('Menu') }}"
                :aria-expanded="mobileOpen.toString()"
                class="min-[1360px]:hidden flex h-10 w-10 items-center justify-center rounded-lg text-white/90
                    transition-colors duration-200 hover:bg-white/10
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div
        class="min-[1360px]:hidden bg-darkbg/95 backdrop-blur-md border-t border-white/10"
        x-show="mobileOpen"
        x-cloak
        x-transition>
        <div class="px-6 py-6 space-y-1 text-sm">

            @foreach ($menus as $i => $menu)
                @php
                    $parentActive = $isMenuActive($menu);
                @endphp

                {{-- TANPA SUBMENU --}}
                @if (!isset($menu['sub']) || empty($menu['sub']))
                    <a wire:navigate.hover
                        href="{{ isset($menu['route']) ? route($menu['route']) : '#' }}"
                        class="block py-2.5 uppercase tracking-wide rounded-md transition-colors
                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50
                            {{ $parentActive ? 'text-primary' : 'text-white/80 hover:text-primary' }}">
                        {{ __($menu['label']) }}
                    </a>

                {{-- DENGAN SUBMENU --}}
                @else
                    <div class="text-white/80 border-t border-white/5 first:border-t-0" x-data="{ open: false }">

                        <button
                            class="flex w-full items-center justify-between py-2.5 uppercase tracking-wide transition-colors
                                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 rounded-md
                                {{ $parentActive ? 'text-primary' : 'hover:text-primary' }}"
                            :aria-expanded="open.toString()"
                            @click="open = !open">
                            <span>{{ __($menu['label']) }}</span>

                            <svg class="w-4 h-4 transition-transform duration-300"
                                :class="open ? 'rotate-180 text-primary' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            class="pl-4 pb-2 space-y-1 border-l border-white/10 ml-1"
                            x-show="open"
                            x-cloak
                            x-transition>
                            @foreach ($menu['sub'] as $sub)
                                @php
                                    $subUrl = isset($sub['param']) ? route($sub['route'], $sub['param']) : route($sub['route']);
                                    $subActive = $subUrl === $currentUrl;
                                @endphp
                                <a wire:navigate.hover
                                    href="{{ $subUrl }}"
                                    @if ($subActive) aria-current="page" @endif
                                    class="block py-1.5 transition-colors rounded-md
                                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50
                                        {{ $subActive ? 'text-primary font-semibold' : 'text-white/65 hover:text-primary' }}
                                        {{ $loop->first ? 'pt-2' : '' }}">
                                    {{ __($sub['label']) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>

    <!-- Ornamen: garis aksen gradien di dasar header -->
    <div aria-hidden="true" class="absolute inset-x-0 bottom-0 h-px bg-linear-to-r from-transparent via-primary/45 to-transparent"></div>
</header>
