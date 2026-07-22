{{-- resources/views/frontend/disabilitas/index.blade.php --}}
@extends('components.layouts.frontend')

@section('title', 'LAYANAN DISABILITAS - JDIH Kota Kendari')
@section('description', 'Portal pencarian dokumen hukum disabilitas Pemerintah Kota Kendari')
@section('image', asset('assets/img/logo-jdih.png'))

@push('styles')
<style>
    /* Hanya state yang di-toggle JavaScript (tidak bisa jadi utility Tailwind statis)
       + styling output partial pagination shared (vendor/pagination/custom).
       Semua tampilan lain memakai Tailwind di markup.
       Warna di sini turunan palet brand: primary #ff891e, primary-hover #ea8221, accent #015BA5. */

    /* Kartu sedang dibacakan (kelas .reading ditambah JS) */
    .document-card.reading {
        background: rgba(255, 137, 30, 0.08);
        border-color: rgba(255, 137, 30, 0.5);
    }

    /* Tombol mic saat mendengarkan (kelas .listening ditambah JS) */
    #mainVoiceBtn.listening {
        color: #ea8221;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 137, 30, 0.4); }
        50%      { box-shadow: 0 0 0 6px rgba(255, 137, 30, 0); }
    }

    /* Panel suara: ringkas jadi tombol, buka saat hover/fokus/geser */
    #voicePanel .voice-expanded { display: none; }
    #voicePanel:hover .voice-expanded,
    #voicePanel:focus-within .voice-expanded,
    #voicePanel.dragging .voice-expanded { display: block; }

    /* Panel suara saat digeser (kelas .dragging ditambah JS) */
    #voicePanel.dragging .voice-expanded {
        box-shadow: 0 16px 40px rgba(1, 91, 165, 0.28);
        opacity: .95;
    }

    /* Status suara aktif (kelas .active ditambah JS) */
    #voiceStatusText.active { color: #015BA5; font-weight: 600; }

    /* Modal abstrak — buka/tutup via kelas .show dari JS */
    .abstract-modal { display: none; }
    .abstract-modal.show { display: flex; }

    /* Pagination — men-styling markup partial shared vendor/pagination/custom */
    .pagination { display: inline-flex; gap: .375rem; }
    .page-link {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 2.5rem; padding: .5rem .75rem;
        background: #fff; border: 1px solid #e2e8f0; border-radius: .25rem;
        color: #475569; font-weight: 500; text-decoration: none; transition: all .2s;
    }
    .page-link:hover { background: #f1f5f9; border-color: #cbd5e1; color: #015BA5; }
    .page-link.active { background: #015BA5; border-color: #015BA5; color: #fff; }
    .page-link.disabled { opacity: .5; pointer-events: none; }
</style>
@endpush

@section('content')
    {{-- Voice Panel — ringkas jadi tombol, membuka saat hover/fokus/geser.
         Posisi & drag ditangani JS via #voicePanel & .drag-handle. --}}
    <div id="voicePanel"
         {{-- Mobile: di atas baris panel aksesibilitas. sm+: di samping kolom panel. --}}
         class="fixed bottom-20 left-4 sm:bottom-4 sm:left-[4.5rem] z-[9999] h-12 w-12 select-none">

        {{-- Ringkas (default): tombol bulat --}}
        <div class="voice-collapsed flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-accent text-white shadow-lg shadow-accent/30 ring-1 ring-white/20 transition hover:bg-accent-hover"
             tabindex="0" role="button" aria-label="{{ __('Mode Suara') }}">
            <i class="fas fa-volume-up"></i>
        </div>

        {{-- Diperluas: panel penuh (membuka ke kanan-atas, dari sudut kiri-bawah tombol) --}}
        <div class="voice-expanded absolute bottom-0 left-0 w-56 overflow-hidden rounded bg-white p-4 shadow-xl shadow-[#012a4d]/15 ring-1 ring-accent/25">
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary to-accent"></div>

            <div class="mb-3 flex items-center justify-between border-b border-slate-200 pb-2.5">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                    <i class="fas fa-volume-up text-accent"></i>
                    {{ __('Mode Suara') }}
                </div>
                <div class="drag-handle cursor-move p-1 text-slate-400 transition hover:text-accent">
                    <i class="fas fa-arrows-alt"></i>
                </div>
            </div>

            <div class="mb-2.5 flex items-center justify-between">
                <span class="text-sm text-slate-600">{{ __('Suara Otomatis') }}</span>
                <label class="relative inline-flex h-6 w-11 cursor-pointer items-center">
                    <input type="checkbox" id="voiceToggle" class="peer sr-only">
                    <span class="absolute inset-0 rounded-full bg-slate-300 transition-colors peer-checked:bg-accent"></span>
                    <span class="absolute left-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                </label>
            </div>

            <div id="voiceStatusText" class="mt-1 text-center text-xs text-slate-500">Mati</div>

            <button id="testVoiceBtn"
                    class="mt-2.5 flex w-full items-center justify-center gap-1.5 rounded border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-primary/50 hover:bg-primary/5 hover:text-primary">
                <i class="fas fa-play"></i>
                {{ __('Test Suara') }}
            </button>
        </div>
    </div>

    {{-- Breadcrumb + Hero (komponen standar situs) --}}
    <x-frontend.breadcrumb
        :title="__('Layanan Disabilitas')"
        :listNav="[
            ['label' => __('Layanan Disabilitas')],
        ]" />

    {{-- Wrapper konten dengan latar & ornamen brand --}}
    <div class="relative bg-linear-to-b from-white via-slate-50 to-slate-100 pb-16">

        {{-- Ornamen abstrak lembut (palet brand) --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -right-20 h-80 w-80 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute top-1/2 -left-28 h-96 w-96 rounded-full bg-accent/5 blur-3xl"></div>
        </div>

        {{-- Kartu pencarian (overlap hero untuk kedalaman) --}}
        <div class="animate-rise relative z-10 mx-auto mt-10 max-w-3xl overflow-hidden rounded border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl"></div>

            <span class="relative mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-lg text-primary">
                <i class="fas fa-universal-access"></i>
            </span>
            <h1 class="relative text-center text-2xl font-bold text-slate-900">{{ __('Cari dokumen disabilitas') }}</h1>
            <p class="relative mb-6 mt-1.5 text-center text-slate-500">{{ __('Temukan semua dokumen hukum terkait disabilitas Kota Kendari') }}</p>

            {{-- Kotak pencarian --}}
            <div class="relative">
                <form id="searchForm" method="GET" action="{{ route('frontend.disabilitas.index') }}">
                    <input type="text"
                           id="mainSearchInput"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="{{ __('Ketik kata kunci atau gunakan suara...') }}"
                           class="w-full rounded border border-slate-200 bg-slate-50 py-3 pl-5 pr-24 text-slate-900 transition focus:border-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/25">
                    <button type="button" id="mainVoiceBtn" title="{{ __('Pencarian suara') }}"
                            class="absolute right-12 top-1/2 -translate-y-1/2 rounded p-1.5 text-slate-400 transition hover:text-accent">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button type="button" id="clearSearchBtn" title="{{ __('Hapus pencarian') }}"
                            class="absolute right-3 top-1/2 hidden -translate-y-1/2 rounded p-1.5 text-slate-400 transition hover:text-red-500">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>

            {{-- Tombol aksi pencarian --}}
            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                <button type="submit" form="searchForm" id="searchNowBtn"
                        class="flex flex-1 items-center justify-center gap-2 rounded bg-primary px-4 py-2.5 font-semibold text-white shadow-lg shadow-primary/30 transition hover:-translate-y-0.5 hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <i class="fas fa-search"></i>
                    {{ __('Cari Sekarang') }}
                </button>
                <button id="toggleFilterBtn"
                        class="flex items-center justify-center gap-2 rounded border border-accent/35 bg-white px-4 py-2.5 font-semibold text-accent transition hover:border-accent hover:bg-accent/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/30">
                    <i class="fas fa-filter"></i>
                    {{ __('Filter') }}
                </button>
            </div>

            {{-- Panel filter (disembunyikan default; buka/tutup via JS: #filterSection) --}}
            <div id="filterSection" class="mt-5 hidden rounded border border-slate-200 bg-slate-50 p-5 transition-all duration-300">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="flex flex-col">
                        <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-700">
                            <i class="fas fa-folder text-accent"></i>
                            {{ __('Jenis Dokumen') }}
                        </label>
                        <select id="docTypeFilter" name="jenis"
                                class="rounded border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="">{{ __('Semua Jenis') }}</option>
                            <option value="uu" {{ request('jenis') == 'uu' ? 'selected' : '' }}>{{ __('Undang-Undang') }}</option>
                            <option value="pp" {{ request('jenis') == 'pp' ? 'selected' : '' }}>{{ __('Peraturan Pemerintah') }}</option>
                            <option value="perpres" {{ request('jenis') == 'perpres' ? 'selected' : '' }}>{{ __('Peraturan Presiden') }}</option>
                            <option value="permen" {{ request('jenis') == 'permen' ? 'selected' : '' }}>{{ __('Peraturan Menteri') }}</option>
                            <option value="perda" {{ request('jenis') == 'perda' ? 'selected' : '' }}>{{ __('Peraturan Daerah') }}</option>
                            <option value="keppres" {{ request('jenis') == 'keppres' ? 'selected' : '' }}>{{ __('Keputusan Presiden') }}</option>
                            <option value="kepmen" {{ request('jenis') == 'kepmen' ? 'selected' : '' }}>{{ __('Keputusan Menteri') }}</option>
                            <option value="se" {{ request('jenis') == 'se' ? 'selected' : '' }}>{{ __('Surat Edaran') }}</option>
                            <option value="juknis" {{ request('jenis') == 'juknis' ? 'selected' : '' }}>{{ __('Petunjuk Teknis') }}</option>
                            <option value="panduan" {{ request('jenis') == 'panduan' ? 'selected' : '' }}>{{ __('Panduan') }}</option>
                            <option value="laporan" {{ request('jenis') == 'laporan' ? 'selected' : '' }}>{{ __('Laporan') }}</option>
                            <option value="kajian" {{ request('jenis') == 'kajian' ? 'selected' : '' }}>{{ __('Studi/Kajian') }}</option>
                            <option value="naskah_akademik" {{ request('jenis') == 'naskah_akademik' ? 'selected' : '' }}>{{ __('Naskah Akademik') }}</option>
                            <option value="rancangan" {{ request('jenis') == 'rancangan' ? 'selected' : '' }}>{{ __('Rancangan Peraturan') }}</option>
                            <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>{{ __('Lainnya') }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-700">
                            <i class="fas fa-calendar text-accent"></i>
                            {{ __('Tahun') }}
                        </label>
                        <select id="yearFilter" name="tahun"
                                class="rounded border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="">{{ __('Semua Tahun') }}</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="mb-2 flex items-center gap-1.5 text-sm font-medium text-slate-700">
                            <i class="fas fa-hashtag text-accent"></i>
                            {{ __('Nomor Dokumen') }}
                        </label>
                        <input type="text"
                               id="docNumberFilter"
                               name="nomor"
                               value="{{ request('nomor') }}"
                               placeholder="{{ __('Contoh: 5, 12, 8') }}"
                               class="rounded border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button id="applyFilterBtn"
                            class="flex flex-1 items-center justify-center gap-2 rounded bg-primary px-4 py-2.5 font-semibold text-white shadow-lg shadow-primary/30 transition hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                        <i class="fas fa-check"></i>
                        {{ __('Terapkan Filter') }}
                    </button>
                    <button id="resetFilterBtn"
                            class="flex items-center justify-center gap-2 rounded border border-accent/35 bg-white px-4 py-2.5 font-semibold text-accent transition hover:border-accent hover:bg-accent/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/30">
                        <i class="fas fa-redo"></i>
                        {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Hasil Dokumen --}}
        <div id="documents" class="documents-section relative z-10 mx-auto mt-12 max-w-6xl px-4">
            <div class="mb-8 text-center">
                <span class="inline-flex items-center gap-2 rounded bg-accent/10 px-3.5 py-1 text-xs font-semibold text-accent">
                    <i class="fas fa-folder-open"></i> {{ __('Daftar Dokumen') }}
                </span>
                <h2 class="mt-3 text-2xl font-bold text-slate-900 md:text-3xl">{{ __('Dokumen disabilitas') }}</h2>
                <span class="mt-3 inline-block h-1 w-14 rounded bg-primary"></span>
            </div>

            <div id="documentsContainer">
                @if($disabilitas->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($disabilitas as $doc)
                            <div class="document-card group relative flex h-full cursor-pointer flex-col overflow-hidden rounded border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-accent/40 hover:shadow-lg hover:shadow-[#012a4d]/10"
                                 data-doc-id="{{ $doc->id }}">
                                <div aria-hidden="true" class="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-linear-to-r from-primary to-accent transition-transform duration-300 group-hover:scale-x-100"></div>

                                <span class="document-type mb-3 inline-flex w-fit items-center gap-1.5 rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">
                                    <i class="fas fa-scale-balanced text-[0.7rem]"></i>{{ $doc->jenis_dokumen_formatted }}
                                </span>
                                <h3 class="document-title mb-2 line-clamp-3 text-base font-semibold leading-snug text-slate-800 transition-colors group-hover:text-accent">{{ $doc->judul }}</h3>
                                <p class="abstract-preview line-clamp-2 text-sm leading-relaxed text-slate-500">
                                    {{ $doc->abstrak ? Str::limit($doc->abstrak, 150) : __('Tidak ada abstrak') }}
                                </p>

                                {{-- meta didorong ke bawah agar tombol sejajar antar kartu --}}
                                <div class="document-meta mt-auto flex flex-wrap items-center gap-x-4 gap-y-1 pt-4 text-xs text-slate-500">
                                    <span><i class="far fa-calendar mr-1 text-slate-400"></i> {{ $doc->tahun }}</span>
                                    <span><i class="far fa-file mr-1 text-slate-400"></i> {{ $doc->jumlah_halaman ? $doc->jumlah_halaman . ' ' . __('halaman') : '-' }}</span>
                                    <span><i class="fas fa-file-pdf mr-1 text-slate-400"></i> PDF</span>
                                </div>

                                {{-- aksi: tonal lembut, terisi solid saat hover (palet brand) --}}
                                <div class="mt-3 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                                    <button class="action-btn abstract-btn flex items-center justify-center gap-1.5 rounded bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300"
                                            data-doc-id="{{ $doc->id }}">
                                        <i class="fas fa-file-lines"></i>{{ __('Abstract') }}
                                    </button>
                                    <a href="{{ route('frontend.disabilitas.show', $doc->id) }}"
                                       class="action-btn flex items-center justify-center gap-1.5 rounded bg-accent/10 px-3 py-2 text-sm font-semibold text-accent transition hover:bg-accent hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40">
                                        <i class="fas fa-eye"></i>{{ __('Detail') }}
                                    </a>
                                    @if($doc->dokumen_utama)
                                        <a href="{{ route('frontend.disabilitas.download', ['id' => $doc->id, 'type' => 'dokumen']) }}" target="_blank"
                                           class="action-btn col-span-2 flex items-center justify-center gap-1.5 rounded bg-primary/10 px-3 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                                            <i class="fas fa-download"></i>{{ __('Download PDF') }}
                                        </a>
                                    @else
                                        <span class="col-span-2 flex cursor-not-allowed items-center justify-center gap-1.5 rounded border border-dashed border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-400">
                                            <i class="fas fa-ban"></i>{{ __('File Tidak Tersedia') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($disabilitas->hasPages())
                        <div class="mt-10 text-center">
                            {{ $disabilitas->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="rounded border border-dashed border-slate-300 bg-slate-50 px-8 py-14 text-center">
                        <span class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-2xl text-primary">
                            <i class="fas fa-folder-open"></i>
                        </span>
                        <h3 class="mb-1.5 text-lg font-semibold text-slate-700">{{ __('Belum ada dokumen') }}</h3>
                        <p class="mx-auto max-w-md text-slate-500">{{ __('Coba ubah kata kunci atau atur ulang filter untuk melihat dokumen disabilitas lainnya.') }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- /wrapper konten brand --}}

    {{-- Modal Abstrak — buka/tutup via kelas .show (JS) --}}
    <div id="abstractModal" class="abstract-modal fixed inset-0 z-[10000] items-center justify-center bg-black/50 p-4">
        <div class="max-h-[80vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-2xl sm:p-8">
            <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 id="abstractModalTitle" class="text-lg font-bold text-slate-800">{{ __('Abstract Dokumen') }}</h3>
                <button id="closeAbstractModal" class="text-slate-400 transition hover:text-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="abstractModalContent" class="leading-relaxed text-slate-600">
                {{-- Konten abstract dimuat via JS --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // SISTEM SUARA PEREMPUAN INDONESIA/ASIA YANG JELAS
    class IndonesiaVoiceSystem {
        constructor() {
            this.isVoiceEnabled = false;
            this.indonesianFemaleVoice = null;
            this.currentDocumentCard = null;
            this.hoverTimeout = null;
            this.debounceTime = 300; // Delay untuk hover

            this.init();
        }

        async init() {
            await this.loadVoices();
            this.setupEventListeners();
            this.loadSavedSettings();
        }

        async loadVoices() {
            return new Promise((resolve) => {
                const loadVoicesList = () => {
                    const voices = speechSynthesis.getVoices();
                    console.log('Suara yang tersedia:', voices);

                    // Mencari suara perempuan Indonesia/Asia yang terbaik
                    this.indonesianFemaleVoice = this.findBestIndonesianVoice(voices);

                    if (this.indonesianFemaleVoice) {
                        console.log('Suara perempuan Indonesia dipilih:', this.indonesianFemaleVoice.name);
                    } else {
                        console.warn('Tidak ada suara perempuan Indonesia yang ditemukan');
                    }

                    resolve();
                };

                if (speechSynthesis.getVoices().length > 0) {
                    loadVoicesList();
                } else {
                    speechSynthesis.onvoiceschanged = loadVoicesList;
                }
            });
        }

        // Mencari suara perempuan Indonesia/Asia terbaik
        findBestIndonesianVoice(voices) {
            const voicePriority = [
                // 1. Suara perempuan Indonesia spesifik
                v => (v.lang === 'id-ID' || v.lang.startsWith('id')) &&
                     (v.name.toLowerCase().includes('perempuan') ||
                      v.name.toLowerCase().includes('indonesian female') ||
                      v.name.toLowerCase().includes('id-female')),

                // 2. Suara Google Indonesia
                v => v.name.includes('Google') && v.lang.startsWith('id'),

                // 3. Suara perempuan Asia lainnya (Malaysia, Singapore)
                v => (v.lang === 'ms-MY' || v.lang === 'ms') &&
                     v.name.toLowerCase().includes('female'),

                // 4. Suara perempuan dengan aksen Asia
                v => (v.lang === 'en-GB' || v.lang === 'en-AU') &&
                     v.name.toLowerCase().includes('female'),

                // 5. Suara perempuan apa saja (hindari suara laki-laki)
                v => v.name.toLowerCase().includes('female') &&
                     !v.name.toLowerCase().includes('male') &&
                     !v.name.toLowerCase().includes('david') &&
                     !v.name.toLowerCase().includes('mark')
            ];

            for (const priority of voicePriority) {
                const voice = voices.find(priority);
                if (voice) return voice;
            }

            // Fallback: suara pertama yang ada
            return voices.length > 0 ? voices[0] : null;
        }

        setupEventListeners() {
            // Auto-read pada hover dokumen dengan debounce
            document.addEventListener('mouseover', (e) => {
                if (!this.isVoiceEnabled) return;

                const card = e.target.closest('.document-card');
                if (card && card !== this.currentDocumentCard) {
                    clearTimeout(this.hoverTimeout);

                    this.hoverTimeout = setTimeout(() => {
                        this.currentDocumentCard = card;
                        this.readDocumentCard(card);
                    }, this.debounceTime);
                }
            });

            document.addEventListener('mouseout', (e) => {
                if (e.target.closest('.document-card')) {
                    clearTimeout(this.hoverTimeout);
                    setTimeout(() => {
                        this.currentDocumentCard = null;
                    }, 100);
                }
            });

            // Voice toggle event
            document.getElementById('voiceToggle').addEventListener('change', (e) => {
                this.toggleVoice();
            });

            // Test voice button
            document.getElementById('testVoiceBtn').addEventListener('click', () => {
                if (this.isVoiceEnabled) {
                    this.speak('Selamat datang di layanan dokumen disabilitas');
                } else {
                    alert('Aktifkan mode suara terlebih dahulu');
                }
            });
        }

        readDocumentCard(card) {
            const title = card.querySelector('.document-title')?.textContent || '';
            const type = card.querySelector('.document-type')?.textContent || '';
            const abstract = card.querySelector('.abstract-preview')?.textContent || '';
            const year = card.querySelector('.document-meta span:first-child')?.textContent?.replace('far fa-calendar mr-1', '').trim() || '';

            // Bangun teks dengan bahasa Indonesia yang natural dan jelas
            let speechText = '';

            if (type) speechText += `Dokumen ${type}. `;
            if (title) speechText += `Judul: ${title}. `;
            if (abstract && abstract !== 'Tidak ada abstrak') {
                speechText += `Abstrak: ${abstract}. `;
            }
            if (year) speechText += `Tahun ${year}. `;

            speechText += 'Silakan klik untuk melihat detail.';

            this.speak(speechText);
        }

        speak(text) {
            if (!this.isVoiceEnabled || !text || !window.speechSynthesis) return;

            // Hentikan semua suara yang sedang berjalan
            this.stopAllSpeech();

            setTimeout(() => {
                const utterance = new SpeechSynthesisUtterance(text);

                // Set bahasa Indonesia
                utterance.lang = 'id-ID';

                // Gunakan suara perempuan Indonesia jika tersedia
                if (this.indonesianFemaleVoice) {
                    utterance.voice = this.indonesianFemaleVoice;
                }

                // Optimasi parameter untuk kejelasan suara Indonesia
                utterance.rate = 1.1; // Kecepatan bicara natural (tidak lambat)
                utterance.pitch = 1.1; // Pitch perempuan yang natural
                utterance.volume = 1.0;

                // Jeda antar kalimat untuk kejelasan
                const sentences = text.split('. ');
                utterance.text = sentences.join('.  '); // Tambah spasi ekstra

                // Event handlers
                utterance.onstart = () => {
                    // Highlight kartu yang sedang dibaca
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.add('reading');
                    }
                };

                utterance.onend = () => {
                    // Hapus highlight
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.remove('reading');
                    }
                };

                utterance.onerror = (event) => {
                    console.error('Error membaca teks:', event.error);
                    if (this.currentDocumentCard) {
                        this.currentDocumentCard.classList.remove('reading');
                    }
                };

                // Mulai berbicara
                window.speechSynthesis.speak(utterance);

            }, 100);
        }

        stopAllSpeech() {
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
            }
            // Hapus semua highlight
            document.querySelectorAll('.document-card.reading').forEach(card => {
                card.classList.remove('reading');
            });
        }

        toggleVoice() {
            this.isVoiceEnabled = !this.isVoiceEnabled;

            // Update UI
            const statusText = document.getElementById('voiceStatusText');
            const toggle = document.getElementById('voiceToggle');

            if (statusText) {
                statusText.textContent = this.isVoiceEnabled ? 'Aktif' : 'Mati';
                statusText.classList.toggle('active', this.isVoiceEnabled);
            }

            if (toggle) {
                toggle.checked = this.isVoiceEnabled;
            }

            // Beri feedback suara jika diaktifkan
            if (this.isVoiceEnabled) {
                this.speak('Mode suara diaktifkan. Arahkan kursor ke dokumen untuk mendengarkan deskripsi.');
            } else {
                // Hentikan semua suara saat dimatikan
                this.stopAllSpeech();
            }

            this.saveSettings();
        }

        saveSettings() {
            const settings = {
                enabled: this.isVoiceEnabled
            };
            localStorage.setItem('indonesiaVoiceEnabled', JSON.stringify(settings));
        }

        loadSavedSettings() {
            const saved = localStorage.getItem('indonesiaVoiceEnabled');
            if (saved) {
                try {
                    const settings = JSON.parse(saved);
                    this.isVoiceEnabled = settings.enabled || false;

                    // Update UI
                    const statusText = document.getElementById('voiceStatusText');
                    const toggle = document.getElementById('voiceToggle');

                    if (statusText) {
                        statusText.textContent = this.isVoiceEnabled ? 'Aktif' : 'Mati';
                        statusText.classList.toggle('active', this.isVoiceEnabled);
                    }

                    if (toggle) {
                        toggle.checked = this.isVoiceEnabled;
                    }

                } catch (error) {
                    console.error('Error loading voice settings:', error);
                }
            }
        }
    }

    // SISTEM VOICE RECOGNITION INDONESIA
    class IndonesiaVoiceRecognition {
        constructor() {
            this.isListening = false;
            this.recognition = null;
            this.init();
        }

        init() {
            if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
                console.warn('Voice recognition tidak didukung');
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID'; // Bahasa Indonesia
            this.recognition.interimResults = false;
            this.recognition.maxAlternatives = 1;
            this.recognition.continuous = false;
        }

        startListening() {
            if (!this.recognition) return false;

            this.recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const searchInput = document.getElementById('mainSearchInput');

                // Set nilai input
                searchInput.value = transcript;

                // Tampilkan clear button
                document.getElementById('clearSearchBtn').style.display = 'block';

                // Trigger input event
                searchInput.dispatchEvent(new Event('input', { bubbles: true }));

                // Auto-submit setelah 1.5 detik
                setTimeout(() => {
                    document.getElementById('searchForm').submit();
                }, 1500);
            };

            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                this.stopListening();
            };

            this.recognition.onend = () => {
                this.stopListening();
            };

            try {
                this.recognition.start();
                this.isListening = true;

                // Update UI
                this.updateUI(true);

                return true;
            } catch (error) {
                console.error('Failed to start recognition:', error);
                return false;
            }
        }

        stopListening() {
            if (this.recognition && this.isListening) {
                try {
                    this.recognition.stop();
                } catch (e) {
                    // Ignore stop errors
                }
            }

            this.isListening = false;
            this.updateUI(false);
        }

        toggleListening() {
            if (this.isListening) {
                this.stopListening();
            } else {
                this.startListening();
            }
        }

        updateUI(isListening) {
            const voiceBtn = document.getElementById('mainVoiceBtn');
            if (!voiceBtn) return;

            if (isListening) {
                voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                voiceBtn.classList.add('listening');
                voiceBtn.title = 'Berhenti mendengarkan';
            } else {
                voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                voiceBtn.classList.remove('listening');
                voiceBtn.title = 'Pencarian suara';
            }
        }
    }

    // SISTEM DRAGGABLE VOICE PANEL
    class DraggableVoicePanel {
        constructor() {
            this.panel = document.getElementById('voicePanel');
            this.isDragging = false;
            this.init();
        }

        init() {
            this.setupDraggable();
            this.loadPosition();
        }

        setupDraggable() {
            const handle = this.panel.querySelector('.drag-handle');

            handle.addEventListener('mousedown', (e) => this.startDrag(e));
            document.addEventListener('mousemove', (e) => this.drag(e));
            document.addEventListener('mouseup', () => this.stopDrag());

            // Touch support
            handle.addEventListener('touchstart', (e) => this.startDrag(e.touches[0]));
            document.addEventListener('touchmove', (e) => this.drag(e.touches[0]));
            document.addEventListener('touchend', () => this.stopDrag());
        }

        startDrag(e) {
            this.isDragging = true;
            this.panel.classList.add('dragging');
            this.offsetX = e.clientX - this.panel.offsetLeft;
            this.offsetY = e.clientY - this.panel.offsetTop;
            e.preventDefault();
        }

        drag(e) {
            if (!this.isDragging) return;

            const x = e.clientX - this.offsetX;
            const y = e.clientY - this.offsetY;

            // Boundary check
            const maxX = window.innerWidth - this.panel.offsetWidth;
            const maxY = window.innerHeight - this.panel.offsetHeight;

            this.currentX = Math.max(0, Math.min(x, maxX));
            this.currentY = Math.max(0, Math.min(y, maxY));

            this.panel.style.left = this.currentX + 'px';
            this.panel.style.top = this.currentY + 'px';
        }

        stopDrag() {
            if (this.isDragging) {
                this.isDragging = false;
                this.panel.classList.remove('dragging');
                this.savePosition();
            }
        }

        savePosition() {
            const position = {
                x: this.currentX,
                y: this.currentY
            };
            localStorage.setItem('voicePanelPosition', JSON.stringify(position));
        }

        loadPosition() {
            const saved = localStorage.getItem('voicePanelPosition');
            if (saved) {
                try {
                    const pos = JSON.parse(saved);
                    this.panel.style.left = pos.x + 'px';
                    this.panel.style.top = pos.y + 'px';
                    this.currentX = pos.x;
                    this.currentY = pos.y;
                } catch (error) {
                    console.error('Error loading panel position:', error);
                }
            }
        }
    }

    // SISTEM ABSTRAK
    class AbstractSystem {
        constructor() {
            this.modal = document.getElementById('abstractModal');
            this.init();
        }

        init() {
            this.setupEventListeners();
        }

        setupEventListeners() {
            document.getElementById('closeAbstractModal').addEventListener('click', () => {
                this.closeModal();
            });

            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });
        }

        async openAbstract(docId) {
            try {
                const modal = document.getElementById('abstractModal');
                const modalContent = document.getElementById('abstractModalContent');
                const modalTitle = document.getElementById('abstractModalTitle');

                // Tampilkan loading
                modalTitle.textContent = 'Memuat Abstract...';
                modalContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin text-accent text-2xl mb-2"></i>
                        <p class="text-gray-600">Memuat abstract dokumen...</p>
                    </div>
                `;

                modal.classList.add('show');

                // Ambil data dari API
                const response = await fetch(`{{ url('/') }}/layanan-disabilitas/${docId}/abstract`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Gagal mengambil abstract');

                const data = await response.json();

                if (data.success) {
                    // Cari dokumen di DOM
                    const documentCard = document.querySelector(`.document-card[data-doc-id="${docId}"]`);
                    const title = documentCard?.querySelector('.document-title')?.textContent || 'Dokumen';
                    const type = documentCard?.querySelector('.document-type')?.textContent || 'Dokumen';

                    modalTitle.textContent = `Abstract: ${title}`;
                    modalContent.innerHTML = this.generateAbstractContent({
                        id: docId,
                        title: title,
                        type: type,
                        abstract: data.abstract
                    });

                    // Setup audio button untuk membaca abstract
                    setTimeout(() => {
                        const audioBtn = document.getElementById('playAbstractAudio');
                        if (audioBtn && window.voiceSystem?.isVoiceEnabled) {
                            audioBtn.addEventListener('click', () => {
                                const abstractText = data.abstract || 'Tidak ada abstract tersedia';
                                const audioText = `Abstract dokumen ${title}. ${abstractText}`;
                                window.voiceSystem.speak(audioText);
                            });
                        }
                    }, 100);

                } else {
                    throw new Error('Abstract tidak ditemukan');
                }

            } catch (error) {
                console.error('Error loading abstract:', error);
                document.getElementById('abstractModalContent').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                        <p class="text-gray-600">Gagal memuat abstract. Silakan coba lagi.</p>
                        <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                    </div>
                `;
            }
        }

        generateAbstractContent(doc) {
            const cleanAbstract = doc.abstract || 'Tidak ada abstract tersedia untuk dokumen ini.';

            return `
                <div class="mb-6">
                    <h4 class="font-semibold text-lg mb-2 text-accent">Ringkasan:</h4>
                    <div class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">
                        ${cleanAbstract}
                    </div>
                </div>

                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Jenis:</span>
                        <span class="ml-2 font-semibold">${doc.type}</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-medium text-gray-700">Judul:</span>
                        <span class="ml-2 font-semibold text-sm">${doc.title}</span>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-primary/5 rounded-lg border border-primary/20">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-lg text-accent">
                            <i class="fas fa-volume-up mr-2 text-primary"></i>Dengarkan Abstract:
                        </h4>
                    </div>
                    <button id="playAbstractAudio" class="w-full bg-primary text-white px-4 py-3 rounded-lg hover:bg-primary-hover transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle text-xl"></i>
                        Putar Audio Abstract
                    </button>
                </div>
            `;
        }

        closeModal() {
            this.modal.classList.remove('show');
            // Hentikan suara jika sedang berbicara
            if (window.voiceSystem) {
                window.voiceSystem.stopAllSpeech();
            }
        }
    }

    // SISTEM PENCARIAN UTAMA
    class DocumentSearchSystem {
        constructor() {
            this.voiceSystem = new IndonesiaVoiceSystem();
            this.voiceRecognition = new IndonesiaVoiceRecognition();
            this.draggablePanel = new DraggableVoicePanel();
            this.abstractSystem = new AbstractSystem();

            this.init();
        }

        init() {
            this.setupControls();
            this.setupEventListeners();

            window.voiceSystem = this.voiceSystem;

            // Cek dan scroll ke dokumen jika perlu
            this.checkAndScrollToDocuments();
        }

        setupControls() {
            // Voice search button
            document.getElementById('mainVoiceBtn').addEventListener('click', () => {
                this.voiceRecognition.toggleListening();
            });

            // Clear search button
            document.getElementById('clearSearchBtn').addEventListener('click', () => {
                const searchInput = document.getElementById('mainSearchInput');
                searchInput.value = '';
                searchInput.focus();
                document.getElementById('clearSearchBtn').style.display = 'none';

                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Pencarian dihapus');
                }
            });

            // Search input events
            document.getElementById('mainSearchInput').addEventListener('input', () => {
                const clearBtn = document.getElementById('clearSearchBtn');
                clearBtn.style.display = document.getElementById('mainSearchInput').value ? 'block' : 'none';
            });

            // Filter controls
            document.getElementById('toggleFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleFilterSection();
            });

            document.getElementById('applyFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.applyFilters();
            });

            document.getElementById('resetFilterBtn').addEventListener('click', (e) => {
                e.preventDefault();
                this.resetFilters();
            });

            // Search form submit
            document.getElementById('searchForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }

        setupEventListeners() {
            // Abstract buttons
            document.addEventListener('click', (e) => {
                if (e.target.closest('.abstract-btn')) {
                    e.preventDefault();
                    const docId = e.target.closest('.abstract-btn').getAttribute('data-doc-id');
                    this.abstractSystem.openAbstract(docId);
                }
            });

            // Document card clicks
            document.querySelectorAll('.document-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (!e.target.closest('.action-btn') && !e.target.closest('a')) {
                        const docId = card.getAttribute('data-doc-id');
                        window.location.href = `{{ route('frontend.disabilitas.index') }}/${docId}`;
                    }
                });
            });
        }

        toggleFilterSection() {
            const filterSection = document.getElementById('filterSection');
            const isHidden = filterSection.style.display === 'none' || filterSection.style.display === '';

            if (isHidden) {
                filterSection.style.display = 'block';
                setTimeout(() => {
                    filterSection.style.opacity = '1';
                    filterSection.style.transform = 'translateY(0)';
                }, 10);

                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Filter dibuka. Pilih jenis dokumen, tahun, atau nomor dokumen.');
                }
            } else {
                filterSection.style.opacity = '0';
                filterSection.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    filterSection.style.display = 'none';
                }, 300);

                if (this.voiceSystem.isVoiceEnabled) {
                    this.voiceSystem.speak('Filter ditutup');
                }
            }
        }

        applyFilters() {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            // Tambahkan nilai filter ke form data
            const filterTypes = ['jenis', 'tahun', 'nomor'];
            filterTypes.forEach(type => {
                const filterElement = document.getElementById(type + 'Filter');
                if (filterElement && filterElement.value) {
                    formData.append(type, filterElement.value);
                }
            });

            // Build URL dengan hash #documents
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }

            let url = '{{ route('frontend.disabilitas.index') }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            url += '#documents';

            window.location.href = url;

            if (this.voiceSystem.isVoiceEnabled) {
                this.voiceSystem.speak('Filter diterapkan. Menampilkan dokumen berdasarkan pilihan filter.');
            }
        }

        resetFilters() {
            document.getElementById('docTypeFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('docNumberFilter').value = '';
            document.getElementById('mainSearchInput').value = '';
            document.getElementById('clearSearchBtn').style.display = 'none';

            window.location.href = '{{ route('frontend.disabilitas.index') }}#documents';

            if (this.voiceSystem.isVoiceEnabled) {
                this.voiceSystem.speak('Filter direset. Menampilkan semua dokumen.');
            }
        }

        performSearch() {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            // Build URL dengan hash #documents
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }

            let url = '{{ route('frontend.disabilitas.index') }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            url += '#documents';

            window.location.href = url;
        }

        checkAndScrollToDocuments() {
            // Cek jika ada hash #documents di URL
            if (window.location.hash === '#documents') {
                setTimeout(() => {
                    const documentsSection = document.querySelector('.documents-section');
                    if (documentsSection) {
                        documentsSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        // Beri feedback suara jika ada dokumen
                        if (this.voiceSystem.isVoiceEnabled) {
                            const docCount = document.querySelectorAll('.document-card').length;
                            if (docCount > 0) {
                                this.voiceSystem.speak(`Ditemukan ${docCount} dokumen. Arahkan kursor ke dokumen untuk mendengarkan deskripsi.`);
                            }
                        }
                    }
                }, 500);
            }
        }
    }

    // INISIALISASI SISTEM SAAT HALAMAN DIMUAT
    document.addEventListener('DOMContentLoaded', function() {
        // Tunggu sampai speech synthesis siap
        if (speechSynthesis) {
            speechSynthesis.onvoiceschanged = function() {
                if (!window.documentSearch) {
                    window.documentSearch = new DocumentSearchSystem();
                }
            };
        }

        // Inisialisasi sistem
        window.documentSearch = new DocumentSearchSystem();

        // Tambahkan keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Alt+V untuk toggle voice
            if (e.altKey && e.key === 'v') {
                e.preventDefault();
                window.documentSearch.voiceSystem.toggleVoice();
            }

            // Alt+S untuk voice search
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                window.documentSearch.voiceRecognition.toggleListening();
            }

            // Escape untuk stop semua suara
            if (e.key === 'Escape') {
                if (window.documentSearch.voiceSystem) {
                    window.documentSearch.voiceSystem.stopAllSpeech();
                }
                document.getElementById('abstractModal').classList.remove('show');
            }
        });

    });
</script>
@endpush
