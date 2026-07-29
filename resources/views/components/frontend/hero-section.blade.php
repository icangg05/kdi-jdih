<section class="relative min-h-svh flex items-center justify-center text-white overflow-hidden">

    <!-- BACKGROUND IMAGE -->
    <div class="absolute inset-0">
        <img
            src="{{ asset('assets/img/background.webp') }}"
            alt="Kota Kendari"
            class="w-full h-full object-cover">
        <!-- SCRIM GRADIENT: lebih gelap di bawah untuk depth & keterbacaan -->
        <div class="absolute inset-0 bg-linear-to-b from-black/60 via-black/55 to-black/85"></div>
        <div class="absolute inset-0 bg-darkbg/30"></div>
    </div>

    <!-- ORNAMEN LATAR: glow ambient + grid titik + garis aksen bawah -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-96 w-96 rounded bg-accent/20 blur-3xl"></div>
        <div class="absolute top-1/3 -right-32 h-112 w-md rounded bg-primary/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.14]"
             style="background-image: radial-gradient(rgba(255,255,255,0.6) 1px, transparent 1px); background-size: 32px 32px; -webkit-mask-image: radial-gradient(ellipse at center, black, transparent 75%); mask-image: radial-gradient(ellipse at center, black, transparent 75%);"></div>
        <div class="absolute inset-x-0 bottom-0 h-px bg-linear-to-r from-transparent via-primary/50 to-transparent"></div>
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 w-full max-w-5xl mx-auto px-4 mt-16 text-center">

        <!-- EYEBROW -->
        <p class="animate-rise inline-flex items-center gap-2 rounded border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] lg:text-xs tracking-[0.2em] uppercase text-slate-200 backdrop-blur-sm mb-6">
            <span class="h-1.5 w-1.5 rounded bg-primary"></span>
            {{ __('Selamat Datang di Situs Resmi') }}
        </p>

        <!-- TITLE -->
        <h1 class="animate-rise text-2xl md:text-4xl lg:text-5xl font-bold leading-[1.1] tracking-tight text-balance mb-5"
            style="animation-delay: .08s">
            {{ __('Jaringan Dokumentasi dan Informasi Hukum') }} <br>
            <span class="text-primary">{{ __('Kota Kendari') }}</span>
        </h1>

        <div class="animate-rise w-16 h-1 bg-primary rounded-full mx-auto mb-6" style="animation-delay: .12s"></div>

        <!-- QUOTE -->
        <p class="animate-rise text-sm lg:text-base italic text-slate-200 max-w-3xl mx-auto mb-9" style="animation-delay: .16s">
            "Inae konasara ie'e pinesara inae lia" <br>
            <span class="not-italic text-slate-300">{{ __('Siapa yang menghargai adat ia akan dihormati') }}</span>
        </p>

        <!-- SEARCH: pencarian biasa + Tanya AI (satu input, dua tombol) -->
        {{-- z-20: .animate-rise membuat stacking context, tanpa ini dropdown saran tertutup blok survei --}}
        <div class="animate-rise relative z-20 max-w-4xl mx-auto" style="animation-delay: .24s">
            <form id="globalSearchForm" method="GET" action="{{ route('frontend.dokumen.index', 'peraturan') }}"
                class="relative rounded bg-black/40 p-2 shadow-2xl ring-1 ring-white/15 backdrop-blur-md">
                <div class="flex flex-col md:flex-row items-stretch gap-2">

                    <!-- INPUT -->
                    <div class="relative flex-1">
                        <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input
                            type="text"
                            name="q"
                            id="globalSearchInput"
                            value="{{ request('q') }}"
                            autocomplete="off"
                            placeholder="{{ config('services.ai_search.enabled') ? __('Cari peraturan, putusan, atau tanyakan ke AI...') : __('Cari peraturan atau putusan...') }}"
                            class="w-full rounded bg-white py-3.5 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 outline-none ring-2 ring-transparent transition focus:ring-primary">

                        <!-- kategori default + mirror input untuk AI (dibaca ai-search-system.js) -->
                        <input type="hidden" name="kategori" id="searchKategori" value="peraturan">
                        @if(config('services.ai_search.enabled'))
                        <input type="hidden" id="aiSearchInput">
                        @endif

                        <!-- SUGGESTION DROPDOWN -->
                        <div id="searchSuggestions" role="listbox"
                             class="absolute left-0 right-0 top-full z-50 mt-2 hidden overflow-hidden rounded border border-slate-200 bg-white text-left shadow-2xl"></div>
                    </div>

                    <!-- DUA TOMBOL -->
                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 rounded bg-primary px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-primary-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-black/40">
                            <i class="fas fa-magnifying-glass"></i>
                            <span class="whitespace-nowrap">{{ __('Cari Dokumen') }}</span>
                        </button>
                        @if(config('services.ai_search.enabled'))
                        <button
                            type="button"
                            id="performAiSearch"
                            class="group/ai flex-1 md:flex-none inline-flex items-center justify-center gap-2 rounded bg-accent px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-accent-hover active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-black/40">
                            <i class="fas fa-robot transition-transform duration-300 group-hover/ai:-translate-y-0.5"></i>
                            <span class="whitespace-nowrap">{{ __('Tanya AI') }}</span>
                        </button>
                        @endif
                    </div>
                </div>
            </form>

            <!-- BARIS BANTU: hint AI + filter lanjutan -->
            <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-xs text-slate-300">
                @if(config('services.ai_search.enabled'))
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-wand-magic-sparkles text-primary"></i>
                    {{ __('Ketik lalu tekan Tanya AI untuk jawaban dengan bantuan AI') }}
                </span>
                <span class="hidden sm:inline text-white/25">&bull;</span>
                @endif
                <button
                    type="button"
                    id="toggleFilterBtn"
                    class="inline-flex items-center justify-center gap-2 rounded border border-white/15 bg-white/5 px-3 py-1.5 font-medium text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/50">
                    <i class="fas fa-sliders"></i>
                    {{ __('Filter Pencarian Lanjutan') }}
                </button>
            </div>
        </div>

        <!-- SURVEI KEPUASAN -->
        <div class="animate-rise mt-8" style="animation-delay: .32s">
            <button type="button"
               onclick="bukaModalSurveiJDIH()"
               class="inline-flex items-center justify-center gap-3 rounded border border-white/15 bg-white/10 px-6 py-3 font-semibold text-white shadow-lg backdrop-blur-sm transition hover:bg-white/15 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent">
                <i class="fas fa-clipboard-check text-lg text-primary"></i>
                <span class="text-sm md:text-base">{{ __('ISI SURVEI KEPUASAN') }}</span>
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
            {{-- Ikon dibuat inline (bukan flex item) agar tetap menempel di teks saat teks membungkus di mobile --}}
            <p class="mt-2 text-center text-xs text-slate-300 md:text-sm">
                <i class="fas fa-info-circle mr-1.5"></i>{{ __('Bantu kami meningkatkan kualitas layanan JDIH dengan mengisi survei singkat') }}
            </p>
        </div>
    </div>

</section>

<!-- MODAL SURVEI KEPUASAN JDIH -->
{{-- Tinggi dikunci ke viewport: overlay h-dvh (bukan h-screen, agar aman dari
     bilah alamat mobile), panel h-full + flex-col, dan hanya area iframe yang
     memakai sisa ruang lewat flex-1 + min-h-0. Tidak ada overflow di overlay. --}}
<div id="modalSurveiJDIH"
    role="dialog" aria-modal="true" aria-labelledby="judulModalSurvei"
    class="fixed inset-0 h-dvh bg-black/80 z-999999 hidden items-center justify-center p-3 md:p-6"
    style="backdrop-filter: blur(4px);">
    <div class="flex h-full w-full max-w-4xl flex-col overflow-hidden rounded bg-white shadow-2xl ring-1 ring-white/10 animate-modalSlideIn">

        <!-- Header Modal -->
        <div class="flex shrink-0 items-center justify-between gap-4 bg-accent px-4 py-3 md:px-6 md:py-4">
            <h3 id="judulModalSurvei" class="flex min-w-0 items-center gap-2.5 text-base font-bold text-white md:text-lg">
                <i class="fas fa-star shrink-0 text-primary"></i>
                <span class="truncate">{{ __('Berikan Penilaian Terbaik Anda') }}</span>
            </h3>
            <button onclick="tutupModalSurveiJDIH()"
                    aria-label="{{ __('Tutup') }}"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded bg-white/10 text-white/80 transition duration-300 hover:rotate-90 hover:bg-white/20 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body Modal - Iframe Survei (mengisi sisa tinggi) -->
        <div class="min-h-0 w-full flex-1">
            <iframe
                src="https://surveidigital.spbe.go.id/embed/survey/eyJzdXJ2ZXlfaWQiOjIsInNlcnZpY2VfaWQiOjE3NCwiaG9zdCI6Imh0dHBzOi8vamRpaC5rZW5kYXJpa290YS5nby5pZCxodHRwOi8vamRpaGtlbmRhcmkudGVzdCIsImtleSI6Ijc3elJnSkFrIn0=/embed/view/"
                class="block h-full w-full border-0"
                title="Survei Kepuasan Pengguna Layanan Digital JDIH"
                allow="fullscreen"
                loading="lazy">
            </iframe>
        </div>

        <!-- Footer Modal -->
        <div class="shrink-0 border-t border-gray-200 bg-gray-50 px-4 py-2.5 text-center">
            <p class="text-xs text-gray-600">
                <i class="fas fa-balance-scale mr-1.5 text-accent"></i>
                {{ __('Survei oleh KemenPAN-RB - Layanan Digital Pemerintah') }}
            </p>
        </div>

    </div>
</div>

<!-- MODAL FILTER PENCARIAN LANJUTAN -->
<div id="filterModal" class="fixed inset-0 bg-black/50 z-9999 hidden items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" data-aos="zoom-in" data-aos-duration="300">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800">
                <i class="fas fa-filter text-primary mr-2"></i>
                Filter Pencarian Lanjutan
            </h3>
            <button id="closeFilterModal" class="text-slate-500 hover:text-slate-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6">
            <form id="advancedFilterForm">
                @php
                    $kategoriOptions = [
                        ['value' => 'peraturan', 'label' => __('Peraturan')],
                        ['value' => 'monografi', 'label' => __('Monografi')],
                        ['value' => 'artikel', 'label' => __('Artikel')],
                        ['value' => 'putusan', 'label' => __('Putusan')],
                        ['value' => 'puu', 'label' => __('Perancangan PUU')],
                        ['value' => 'disabilitas', 'label' => __('Layanan Disabilitas')],
                    ];

                    $tahunOptions = collect(range(date('Y'), 2000))
                        ->map(fn($t) => ['value' => (string) $t, 'label' => (string) $t])
                        ->all();

                    $statusOptions = [
                        ['value' => 'berlaku', 'label' => __('Berlaku')],
                        ['value' => 'tidak-berlaku', 'label' => __('Tidak Berlaku')],
                        ['value' => 'dicabut', 'label' => __('Dicabut')],
                        ['value' => 'direvisi', 'label' => __('Direvisi')],
                    ];
                @endphp

                <!-- Filter Kategori -->
                <div class="mb-4">
                    <x-frontend.select-search
                        :wire="false"
                        name="kategori"
                        inputId="filterKategori"
                        :label="__('Kategori')"
                        :placeholder="__('Pilih Kategori')"
                        icon="fa-solid fa-folder-open"
                        :options="$kategoriOptions"
                        :searchPlaceholder="__('Cari kategori...')" />
                </div>

                <!-- Filter Tahun -->
                <div class="mb-4">
                    <x-frontend.select-search
                        :wire="false"
                        name="tahun"
                        inputId="filterTahun"
                        :label="__('Tahun')"
                        :placeholder="__('Pilih Tahun')"
                        icon="fa-regular fa-calendar"
                        :options="$tahunOptions"
                        :searchPlaceholder="__('Cari tahun...')" />
                </div>

                <!-- Filter Nomor -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Dokumen</label>
                    <input
                        type="text"
                        id="filterNomor"
                        name="nomor"
                        placeholder="Masukkan nomor dokumen"
                        class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition">
                </div>

                <!-- Filter Status -->
                <div class="mb-6">
                    <x-frontend.select-search
                        :wire="false"
                        name="status"
                        inputId="filterStatus"
                        :label="__('Status')"
                        :placeholder="__('Pilih Status')"
                        icon="fa-solid fa-circle-check"
                        :options="$statusOptions"
                        :searchPlaceholder="__('Cari status...')" />
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-3 pt-4 border-t">
                    <button
                        type="button"
                        id="applyFilterBtn"
                        class="flex-1 bg-primary hover:bg-primary-hover text-white text-sm font-semibold px-4 py-3 rounded flex items-center justify-center gap-2 transition">
                        <i class="fas fa-filter"></i>
                        TERAPKAN FILTER
                    </button>
                    <button
                        type="button"
                        id="resetFilterBtn"
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold px-4 py-3 rounded flex items-center justify-center gap-2 transition">
                        <i class="fas fa-redo"></i>
                        RESET
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HANYA TAMBAHKAN SATU BARIS INI DI BAWAH SECTION -->
@if(config('services.ai_search.enabled'))
    @include('components.frontend.ai-search-modal')
@endif

@push('styles')
<style>
    /* AI Search Box Styling */
    .ai-search-box {
        background: rgba(15, 23, 42, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        position: relative;
        z-index: 1;
    }

    .ai-search-title {
        color: white;
        text-align: center;
        margin-bottom: 15px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .ai-search-wrapper {
        position: relative;
        margin-bottom: 10px;
    }

    .ai-search-input {
        width: 100%;
        padding: 14px 120px 14px 20px;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        background: white !important;
        color: #000000 !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .ai-search-input:focus {
        outline: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        border-color: #ff891e;
    }

    .ai-search-button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: #ff891e;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(255, 137, 30, 0.25);
    }

    .ai-search-button:hover {
        background: #ea8221;
    }

    .ai-search-examples {
        color: rgba(255,255,255,0.9);
        font-size: 0.8rem;
        text-align: center;
        margin-top: 10px;
        font-style: italic;
    }

    /* Modal Survei Animation */
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .animate-modalSlideIn {
        animation: modalSlideIn 0.4s ease-out;
    }

    /* Modal Filter Styling */
    #filterModal {
        backdrop-filter: blur(4px);
    }

    #filterModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }

    /* Filter Form Styling */
    #advancedFilterForm select,
    #advancedFilterForm input {
        transition: all 0.3s ease;
    }

    #advancedFilterForm select:focus,
    #advancedFilterForm input:focus {
        border-color: #015BA5;
        box-shadow: 0 0 0 3px rgba(1, 91, 165, 0.12);
    }

    #applyFilterBtn, #resetFilterBtn {
        transition: all 0.2s ease;
    }

    #applyFilterBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(255, 137, 30, 0.35);
    }

    #resetFilterBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(100, 116, 139, 0.3);
    }

    /* SARAN PENCARIAN (dropdown saat mengetik) */
    #searchSuggestions {
        max-height: 22rem;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .suggestion-item {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 10px 14px;
        text-align: left;
        cursor: pointer;
        transition: background-color .15s;
    }

    .suggestion-item + .suggestion-item {
        border-top: 1px solid #f1f5f9;
    }

    .suggestion-item:hover,
    .suggestion-item.is-active {
        background-color: #f8fafc;
    }

    .suggestion-item__icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 34px;
        height: 34px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #64748b;
        font-size: .8rem;
    }

    .suggestion-item:hover .suggestion-item__icon,
    .suggestion-item.is-active .suggestion-item__icon {
        background: var(--color-primary, #ff891e);
        color: #fff;
    }

    .suggestion-item__text {
        min-width: 0;
        flex: 1;
    }

    .suggestion-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .875rem;
        font-weight: 600;
        color: #1e293b;
    }

    .suggestion-title span:first-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .suggestion-desc {
        margin-top: 2px;
        font-size: .75rem;
        color: #64748b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .suggestion-badge {
        flex: none;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .badge-disabilitas { background: #e0f2fe; color: #015BA5 }
    .badge-puu { background: #e2e8f0; color: #334155 }
    .badge-default { background: #fff1e3; color: #c2620b }

    .suggestion-hint {
        flex: none;
        padding: 3px 7px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-size: .65rem;
        font-weight: 600;
        color: #94a3b8;
    }

    @media (max-width: 480px) {
        .suggestion-hint { display: none }
    }

    /* Baris "Tanya AI": hanya di mobile, karena tombolnya tertutup dropdown */
    .suggestion-item--ai .suggestion-item__icon {
        background: var(--color-accent, #015BA5);
        color: #fff;
    }

    .suggestion-item--ai .suggestion-title span:first-child {
        color: var(--color-accent, #015BA5);
    }

    @media (min-width: 768px) {
        .suggestion-item--ai { display: none }
        .suggestion-item--ai + .suggestion-item { border-top: 0 }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #globalSearchForm {
            flex-direction: column;
        }

        button[type="submit"] {
            width: 100%;
        }

        .ai-search-input {
            padding: 12px 100px 12px 15px;
            font-size: 0.9rem;
        }

        .ai-search-button {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        #toggleFilterBtn {
            width: 100%;
        }

        /* Header text responsive */
        .absolute.top-0.left-0.right-0 .text-sm {
            font-size: 0.75rem;
            text-align: center;
            padding: 0 10px;
        }
    }

    @media (max-width: 640px) {
        #filterModal .p-6 {
            padding: 1rem;
        }

        #filterModal .flex.gap-3 {
            flex-direction: column;
        }

        #filterModal .flex.gap-3 button {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ========== FUNGSI MODAL SURVEI JDIH ==========
    window.bukaModalSurveiJDIH = function() {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    window.tutupModalSurveiJDIH = function() {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Tutup modal dengan klik di luar
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modalSurveiJDIH');
        if (modal && e.target === modal) {
            tutupModalSurveiJDIH();
        }
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modalSurveiJDIH');
            if (modal && !modal.classList.contains('hidden')) {
                tutupModalSurveiJDIH();
            }
        }
    });

    // ========== JAVASCRIPT KHUSUS PENCARIAN DOKUMEN ==========
// Fungsi ini untuk suggestion dropdown tanpa mengganggu form submit
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ELEMEN PENCARIAN
    const searchInput = document.getElementById('globalSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const searchForm = document.getElementById('globalSearchForm');

    if (!searchInput || !searchSuggestions) {
        return;
    }

    // MIRROR: input gabungan -> #aiSearchInput (dibaca ai-search-system.js)
    const aiMirror = document.getElementById('aiSearchInput');
    if (aiMirror) {
        aiMirror.value = searchInput.value;
        searchInput.addEventListener('input', function () {
            aiMirror.value = this.value;
        });
    }

    // KATA KUNCI UNTUK SUGGESTIONS
    const keywordMap = {
        'disabilitas': {
            name: 'Layanan Disabilitas',
            route: '{{ route("frontend.disabilitas.index") }}',
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'difabel': {
            name: 'Layanan Disabilitas',
            route: '{{ route("frontend.disabilitas.index") }}',
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'cacat': {
            name: 'Layanan Disabilitas',
            route: '{{ route("frontend.disabilitas.index") }}',
            badge: 'badge-disabilitas',
            desc: 'Temukan dokumen terkait layanan dan hak penyandang disabilitas'
        },
        'puu': {
            name: 'Perancangan PUU',
            route: '{{ route("frontend.pembentukan-puu.index") }}',
            badge: 'badge-puu',
            desc: 'Dokumen proses perancangan Peraturan Undang-Undang'
        },
        'peraturan': {
            name: 'Peraturan',
            route: '{{ route("frontend.dokumen.index", "peraturan") }}',
            badge: 'badge-default',
            desc: 'Cari di kategori peraturan perundang-undangan'
        },
        'monografi': {
            name: 'Monografi',
            route: '{{ route("frontend.dokumen.index", "monografi") }}',
            badge: 'badge-default',
            desc: 'Cari di kategori monografi hukum'
        },
        'artikel': {
            name: 'Artikel',
            route: '{{ route("frontend.dokumen.index", "artikel") }}',
            badge: 'badge-default',
            desc: 'Cari di kategori artikel hukum'
        },
        'putusan': {
            name: 'Putusan',
            route: '{{ route("frontend.dokumen.index", "putusan") }}',
            badge: 'badge-default',
            desc: 'Cari di kategori putusan pengadilan'
        }
    };

    // SARAN PENCARIAN
    const escHtml = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    const ikonBadge = {
        'badge-disabilitas': 'fa-universal-access',
        'badge-puu': 'fa-file-pen',
        'badge-default': 'fa-folder-open'
    };

    function barisSaran({ tag, attrs, extraClass, icon, title, badge, badgeText, desc, hint }) {
        return `
            <${tag} ${attrs} class="suggestion-item ${extraClass || ''}" role="option">
                <span class="suggestion-item__icon"><i class="fas ${icon}"></i></span>
                <span class="suggestion-item__text">
                    <span class="suggestion-title">
                        <span>${title}</span>
                        ${badgeText ? `<span class="suggestion-badge ${badge}">${escHtml(badgeText)}</span>` : ''}
                    </span>
                    <span class="suggestion-desc">${desc}</span>
                </span>
                ${hint ? `<span class="suggestion-hint">${hint}</span>` : ''}
            </${tag}>`;
    }

    function handleSearchInput() {
        const nilai = searchInput.value.trim();

        if (!nilai) {
            searchSuggestions.classList.add('hidden');
            return;
        }

        const query = nilai.toLowerCase();
        const cocok = Object.entries(keywordMap)
            .filter(([keyword]) => query.includes(keyword))
            .slice(0, 4);

        // tombol "Tanya AI" tertutup dropdown di mobile -> sediakan di sini
        let html = barisSaran({
            tag: 'button',
            attrs: 'type="button" data-saran-ai',
            extraClass: 'suggestion-item--ai',
            icon: 'fa-robot',
            title: `Tanya AI: <strong>“${escHtml(nilai)}”</strong>`,
            desc: 'Jawaban ringkas dari AI beserta dokumen rujukannya'
        });

        html += cocok.map(([keyword, data]) => barisSaran({
            tag: 'a',
            attrs: `href="${data.route}?q=${encodeURIComponent(nilai)}"`,
            icon: ikonBadge[data.badge] || 'fa-folder-open',
            title: escHtml(data.name),
            badge: data.badge,
            badgeText: keyword,
            desc: escHtml(data.desc)
        })).join('');

        // opsi terakhir: cari di semua dokumen (submit form)
        html += barisSaran({
            tag: 'button',
            attrs: 'type="submit"',
            icon: 'fa-magnifying-glass',
            title: `Cari <strong>“${escHtml(nilai)}”</strong> di semua dokumen`,
            desc: 'Telusuri seluruh koleksi dokumen JDIH Kota Kendari',
            hint: 'Enter'
        });

        searchSuggestions.innerHTML = html;
        searchSuggestions.classList.remove('hidden');
    }

    // NAVIGASI KEYBOARD (panah atas/bawah, Enter, Escape)
    function pindahSorotan(arah) {
        // offsetParent null = baris yang disembunyikan (mis. baris AI di desktop)
        const items = [...searchSuggestions.querySelectorAll('.suggestion-item')].filter((el) => el.offsetParent);
        if (!items.length) return;

        const kini = items.findIndex((el) => el.classList.contains('is-active'));
        const next = (kini + arah + items.length + (kini === -1 && arah < 0 ? 1 : 0)) % items.length;

        items.forEach((el) => el.classList.remove('is-active'));
        items[next].classList.add('is-active');
        items[next].scrollIntoView({ block: 'nearest' });
    }

    searchInput.addEventListener('keydown', function (e) {
        const terbuka = !searchSuggestions.classList.contains('hidden');

        if (e.key === 'Escape') {
            searchSuggestions.classList.add('hidden');
        } else if (terbuka && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
            e.preventDefault();
            pindahSorotan(e.key === 'ArrowDown' ? 1 : -1);
        } else if (terbuka && e.key === 'Enter') {
            const aktif = searchSuggestions.querySelector('.suggestion-item.is-active');
            if (aktif) {
                e.preventDefault();
                aktif.click();
            }
        }
    });

    // EVENT LISTENER UNTUK INPUT
    searchInput.addEventListener('input', handleSearchInput);

    searchSuggestions.addEventListener('click', function (e) {
        if (e.target.closest('[data-saran-ai]')) {
            searchSuggestions.classList.add('hidden');
            document.getElementById('performAiSearch')?.click();
        }
    });

    // SEMBUNYIKAN SUGGESTIONS SAAT KLIK DI LUAR
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            searchSuggestions.classList.add('hidden');
        }
    });

    // ISI ULANG NILAI SEARCH DARI URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('q');
    if (searchQuery) {
        searchInput.value = searchQuery;
    }

});
    // ========== FIXED FILTER MODAL SYSTEM ==========
    (function() {
        'use strict';

        let filterModalInitialized = false;

        function initializeFilterModal() {
            if (filterModalInitialized) {
                return;
            }


            const toggleFilterBtn = document.getElementById('toggleFilterBtn');
            const filterModal = document.getElementById('filterModal');
            const closeFilterModal = document.getElementById('closeFilterModal');

            if (!toggleFilterBtn || !filterModal || !closeFilterModal) {
                console.error('Filter modal elements not found');
                return;
            }

            // Clone elements untuk hapus event listeners lama
            const newToggleBtn = toggleFilterBtn.cloneNode(true);
            toggleFilterBtn.parentNode.replaceChild(newToggleBtn, toggleFilterBtn);

            // Buka modal ketika tombol filter diklik
            newToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                filterModal.classList.remove('hidden');
                filterModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });

            // Tutup modal ketika tombol close diklik
            closeFilterModal.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                filterModal.classList.remove('flex');
                filterModal.classList.add('hidden');
                document.body.style.overflow = '';
            });

            // Tutup modal ketika klik di luar modal
            filterModal.addEventListener('click', function(e) {
                if (e.target === filterModal) {
                    filterModal.classList.remove('flex');
                    filterModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });

            // Tutup modal dengan tombol ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && filterModal.classList.contains('flex')) {
                    filterModal.classList.remove('flex');
                    filterModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });

            // Setup advanced filters
            initializeAdvancedFilters();

            filterModalInitialized = true;
        }

        function initializeAdvancedFilters() {
            const applyFilterBtn = document.getElementById('applyFilterBtn');
            const resetFilterBtn = document.getElementById('resetFilterBtn');

            if (!applyFilterBtn || !resetFilterBtn) {
                console.error('Filter buttons not found');
                return;
            }

            // Clone buttons untuk hapus event listeners lama
            const newApplyBtn = applyFilterBtn.cloneNode(true);
            applyFilterBtn.parentNode.replaceChild(newApplyBtn, applyFilterBtn);

            const newResetBtn = resetFilterBtn.cloneNode(true);
            resetFilterBtn.parentNode.replaceChild(newResetBtn, resetFilterBtn);

            // Fungsi untuk terapkan filter
            newApplyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                applyAdvancedFilters();
            });

            // Fungsi untuk reset filter
            newResetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('advancedFilterForm').reset();
            });

            // Terapkan filter dengan Enter key pada input nomor
            const filterNomor = document.getElementById('filterNomor');
            if (filterNomor) {
                filterNomor.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyAdvancedFilters();
                    }
                });
            }

        }

        function applyAdvancedFilters() {

            const kategori = document.getElementById('filterKategori')?.value || '';
            const tahun = document.getElementById('filterTahun')?.value || '';
            const nomor = document.getElementById('filterNomor')?.value || '';
            const status = document.getElementById('filterStatus')?.value || '';

            // Validasi minimal satu filter harus diisi
            if (!kategori && !tahun && !nomor && !status) {
                alert('Silakan pilih minimal satu filter untuk diterapkan');
                return;
            }

            // Tentukan URL berdasarkan kategori
            let baseUrl = '';
            const params = new URLSearchParams();

            // Mapping kategori ke route yang sesuai
            const routeMap = {
                'peraturan': '{{ route("frontend.dokumen.index", "peraturan") }}',
                'monografi': '{{ route("frontend.dokumen.index", "monografi") }}',
                'artikel': '{{ route("frontend.dokumen.index", "artikel") }}',
                'putusan': '{{ route("frontend.dokumen.index", "putusan") }}',
                'puu': '{{ route("frontend.pembentukan-puu.index") }}',
                'disabilitas': '{{ route("frontend.disabilitas.index") }}'
            };

            // Jika kategori dipilih, gunakan route khusus
            if (kategori && routeMap[kategori]) {
                baseUrl = routeMap[kategori];
            } else {
                // Default ke peraturan jika kategori tidak dipilih
                baseUrl = '{{ route("frontend.dokumen.index", "peraturan") }}';
            }

            // Tambahkan parameter filter lainnya
            if (tahun) params.append('tahun', tahun);
            if (nomor) params.append('nomor', nomor);
            if (status) params.append('status', status);

            // Tambahkan parameter pencarian dari input utama jika ada
            const searchQuery = document.getElementById('globalSearchInput')?.value || '';
            if (searchQuery) {
                params.append('q', searchQuery);
            } else if (!kategori) {
                // Jika tidak ada kata kunci dan tidak ada kategori, beri pesan
                alert('Silakan masukkan kata kunci pencarian atau pilih kategori');
                return;
            }

            // Tutup modal
            const filterModal = document.getElementById('filterModal');
            if (filterModal) {
                filterModal.classList.remove('flex');
                filterModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Redirect ke URL dengan parameter filter
            const queryString = params.toString();
            window.location.href = baseUrl + (queryString ? '?' + queryString : '');
        }

        // ========== FIXED SMART SEARCH SYSTEM ==========


        // ========== INITIALIZATION ==========
        function setupSearchFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('q');

            if (searchQuery) {
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.value = searchQuery;
                }
            }
        }

        // Main initialization function
        function initializeAllSystems() {

            // Initialize filter modal
            initializeFilterModal();



            // Setup search from URL
            setupSearchFromUrl();

        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAllSystems);
        } else {
            initializeAllSystems();
        }

        // Handle Livewire navigation
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:navigated', function() {
                setTimeout(initializeAllSystems, 100);
            });
        }

        // Expose functions to window
        window.initializeFilterModal = initializeFilterModal;
        window.applyAdvancedFilters = applyAdvancedFilters;

    })(); // End IIFE

</script>

<!-- AI Search System Script - LOAD WITH DEFER -->
@if(config('services.ai_search.enabled'))
<script src="{{ asset('js/ai-search-system.js') }}?v={{ filemtime(public_path('js/ai-search-system.js')) }}" defer></script>
@endif
@endpush
