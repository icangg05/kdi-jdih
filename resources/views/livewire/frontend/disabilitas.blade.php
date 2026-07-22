<div>
    @php
        // Aksi yang memuat ulang daftar dokumen → pemicu skeleton
        $loadingTarget = 'q,jenis,tahun,nomor,resetFilter,gotoPage,previousPage,nextPage';

        $jenisOptions = [
            ['value' => 'uu', 'label' => __('Undang-Undang')],
            ['value' => 'pp', 'label' => __('Peraturan Pemerintah')],
            ['value' => 'perpres', 'label' => __('Peraturan Presiden')],
            ['value' => 'permen', 'label' => __('Peraturan Menteri')],
            ['value' => 'perda', 'label' => __('Peraturan Daerah')],
            ['value' => 'keppres', 'label' => __('Keputusan Presiden')],
            ['value' => 'kepmen', 'label' => __('Keputusan Menteri')],
            ['value' => 'se', 'label' => __('Surat Edaran')],
            ['value' => 'juknis', 'label' => __('Petunjuk Teknis')],
            ['value' => 'panduan', 'label' => __('Panduan')],
            ['value' => 'laporan', 'label' => __('Laporan')],
            ['value' => 'kajian', 'label' => __('Studi/Kajian')],
            ['value' => 'naskah_akademik', 'label' => __('Naskah Akademik')],
            ['value' => 'rancangan', 'label' => __('Rancangan Peraturan')],
            ['value' => 'lainnya', 'label' => __('Lainnya')],
        ];

        $tahunOptions = collect($years)
            ->map(fn($y) => ['value' => (string) $y, 'label' => (string) $y])
            ->values()
            ->all();
    @endphp

    {{-- Hanya state yang di-toggle JavaScript + turunan palet brand (primary #ff891e, primary-hover #ea8221, accent #015BA5) --}}
    <style>
        .document-card.reading { background: rgba(255, 137, 30, 0.08); border-color: rgba(255, 137, 30, 0.5); }
        #mainVoiceBtn.listening { color: #ea8221; animation: pulse 1.5s infinite; }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 137, 30, 0.4); }
            50%      { box-shadow: 0 0 0 6px rgba(255, 137, 30, 0); }
        }
        #voiceStatusText.active { color: #015BA5; font-weight: 600; }

        /* Panel suara: ringkas jadi tombol, membuka/menutup dengan animasi saat hover/fokus */
        #voicePanel .voice-expanded {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(10px) scale(.95);
            transform-origin: bottom left;
            transition: opacity .22s ease, transform .22s cubic-bezier(0.16, 1, 0.3, 1), visibility .22s;
        }
        #voicePanel:hover .voice-expanded,
        #voicePanel:focus-within .voice-expanded {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        /* Tombol ringkas: warna berbeda saat Mode Suara aktif vs nonaktif */
        #voicePanel .voice-collapsed {
            transition: background-color .25s ease, box-shadow .25s ease, transform .22s ease, opacity .18s ease;
        }
        #voicePanel .voice-collapsed:hover { transform: scale(1.07); }

        /* Crossfade: tombol memudar saat panel terbuka */
        #voicePanel:hover .voice-collapsed,
        #voicePanel:focus-within .voice-collapsed {
            opacity: 0;
            transform: scale(.9);
        }
        #voicePanel.voice-active .voice-collapsed {
            background-color: #ff891e;
            animation: voicePulse 2s infinite;
        }
        #voicePanel.voice-active .voice-collapsed:hover { background-color: #ea8221; }
        @keyframes voicePulse {
            0%   { box-shadow: 0 0 0 0 rgba(255, 137, 30, .55); }
            70%  { box-shadow: 0 0 0 12px rgba(255, 137, 30, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 137, 30, 0); }
        }

        /* Modal abstrak — buka/tutup via kelas .show dari JS, .closing untuk animasi keluar */
        .abstract-modal { display: none; }
        .abstract-modal.show { display: flex; animation: modalFadeIn .2s ease-out both; }
        .abstract-modal.show > div { animation: modalPanelIn .28s cubic-bezier(.16, 1, .3, 1) both; }
        .abstract-modal.closing { animation: modalFadeIn .18s ease-in reverse both; }
        .abstract-modal.closing > div { animation: modalPanelOut .18s ease-in both; }

        @keyframes modalFadeIn  { from { opacity: 0 } to { opacity: 1 } }
        @keyframes modalPanelIn  { from { opacity: 0; transform: translateY(12px) scale(.97) } to { opacity: 1; transform: none } }
        @keyframes modalPanelOut { from { opacity: 1; transform: none } to { opacity: 0; transform: translateY(8px) scale(.98) } }

        @media (prefers-reduced-motion: reduce) {
            .abstract-modal.show,
            .abstract-modal.show > div,
            .abstract-modal.closing,
            .abstract-modal.closing > div { animation-duration: .01ms }
        }
    </style>

    {{-- Voice Panel — ringkas jadi tombol, membuka saat hover; murni client-side (wire:ignore) --}}
    <div id="voicePanel" wire:ignore
         {{-- Mobile: di atas baris panel aksesibilitas. sm+: di samping kolom panel. --}}
         class="fixed bottom-20 left-4 sm:bottom-4 sm:left-[4.5rem] z-[9999] h-12 w-12 select-none">

        {{-- Ringkas (default): tombol bulat --}}
        <div class="voice-collapsed flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-accent text-white shadow-lg shadow-accent/30 ring-1 ring-white/20 transition hover:bg-accent-hover"
             tabindex="0" role="button" aria-label="{{ __('Mode Suara') }}">
            <i class="fas fa-volume-up"></i>
        </div>

        {{-- Diperluas: panel penuh (membuka ke kanan-atas) --}}
        <div class="voice-expanded absolute bottom-0 left-0 w-56 overflow-hidden rounded bg-white p-4 shadow-xl shadow-[#012a4d]/15 ring-1 ring-accent/25">
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary to-accent"></div>

            <div class="mb-3 flex items-center gap-2 border-b border-slate-200 pb-2.5 text-sm font-semibold text-slate-800">
                <i class="fas fa-volume-up text-accent"></i>
                {{ __('Mode Suara') }}
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

            <button id="testVoiceBtn" type="button"
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

        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -right-20 h-80 w-80 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute top-1/2 -left-28 h-96 w-96 rounded-full bg-accent/5 blur-3xl"></div>
        </div>

        {{-- ============ KARTU PENCARIAN (reaktif) ============ --}}
        {{-- z-20 (di atas seksi hasil) + TANPA overflow-hidden supaya dropdown select-search tidak terpotong --}}
        <div class="animate-rise relative z-20 mx-auto mt-10 max-w-3xl rounded border border-slate-200 bg-white shadow-sm">
            {{-- Ornamen dikliping di pembungkusnya sendiri, bukan di kartu --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden rounded">
                <div class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>
                <div class="absolute -top-16 -right-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl"></div>
            </div>

            <div class="relative p-6 sm:p-7">
                {{-- Header --}}
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-lg text-primary">
                        <i class="fas fa-universal-access"></i>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">{{ __('Cari dokumen disabilitas') }}</h1>
                        <p class="text-sm text-slate-500">{{ __('Ketik untuk mencari — hasil tampil otomatis') }}</p>
                    </div>
                </div>

                {{-- Input pencarian (debounce Livewire) --}}
                <div class="relative">
                    <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="mainSearchInput"
                           wire:model.live.debounce.400ms="q"
                           placeholder="{{ __('Ketik kata kunci atau gunakan suara...') }}"
                           class="w-full rounded border border-slate-200 bg-slate-50 py-3 pl-11 pr-20 text-slate-900 transition focus:border-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/25">

                    {{-- Spinner saat mengetik --}}
                    <span wire:loading wire:target="q" class="absolute right-12 top-1/2 -translate-y-1/2 text-accent">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </span>
                    {{-- Tombol mikrofon (disembunyikan saat spinner tampil) --}}
                    <button type="button" id="mainVoiceBtn" title="{{ __('Pencarian suara') }}"
                            wire:loading.remove wire:target="q"
                            class="absolute right-12 top-1/2 -translate-y-1/2 rounded p-1.5 text-slate-400 transition hover:text-accent">
                        <i class="fas fa-microphone"></i>
                    </button>

                    @if ($q !== '')
                        <button type="button" wire:click="$set('q', '')" title="{{ __('Hapus pencarian') }}"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded p-1.5 text-slate-400 transition hover:text-red-500">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>

                {{-- Filter reaktif (select-search reusable, auto-apply via @entangle .live) --}}
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <x-frontend.select-search
                        name="jenis"
                        :label="__('Jenis Dokumen')"
                        :placeholder="__('Semua Jenis')"
                        icon="fa-solid fa-layer-group"
                        :options="$jenisOptions"
                        :searchPlaceholder="__('Cari jenis...')" />

                    <x-frontend.select-search
                        name="tahun"
                        :label="__('Tahun')"
                        :placeholder="__('Semua Tahun')"
                        icon="fa-solid fa-calendar"
                        :options="$tahunOptions"
                        :searchPlaceholder="__('Cari tahun...')" />

                    <div class="space-y-1.5">
                        <label class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Nomor') }}</label>
                        <div class="relative">
                            <i class="fa-solid fa-hashtag pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                            <input type="text" wire:model.live.debounce.400ms="nomor"
                                   placeholder="{{ __('cth: 8') }}"
                                   class="w-full rounded border border-gray-300 py-2.5 pl-11 pr-3 text-sm transition focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/10">
                        </div>
                    </div>
                </div>

                {{-- Footer: info hasil + tombol Reset --}}
                <div class="mt-4 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-500">
                        <span wire:loading.remove wire:target="{{ $loadingTarget }}">
                            {{ __('Menampilkan') }} <span class="font-semibold text-slate-700">{{ $disabilitas->total() }}</span> {{ __('dokumen') }}
                        </span>
                        <span wire:loading wire:target="{{ $loadingTarget }}" class="inline-flex items-center gap-1.5">
                            <i class="fas fa-circle-notch fa-spin text-accent"></i> {{ __('Mencari...') }}
                        </span>
                    </p>

                    <button type="button" wire:click="resetFilter" @disabled(!$hasFilter)
                            class="inline-flex items-center gap-2 rounded px-4 py-2 text-sm font-semibold transition focus:outline-none
                                {{ $hasFilter
                                    ? 'bg-primary text-white hover:bg-primary-hover focus:ring-2 focus:ring-primary/40'
                                    : 'cursor-not-allowed bg-slate-100 text-slate-400' }}">
                        <i class="fas fa-rotate-left"></i>
                        {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ============ HASIL DOKUMEN ============ --}}
        <div id="documents" class="documents-section relative z-10 mx-auto mt-12 max-w-6xl px-4">
            <div class="mb-8 text-center">
                <span class="inline-flex items-center gap-2 rounded bg-accent/10 px-3.5 py-1 text-xs font-semibold text-accent">
                    <i class="fas fa-folder-open"></i> {{ __('Daftar Dokumen') }}
                </span>
                <h2 class="mt-3 text-2xl font-bold text-slate-900 md:text-3xl">{{ __('Dokumen disabilitas') }}</h2>
                <span class="mt-3 inline-block h-1 w-14 rounded bg-primary"></span>
            </div>

            {{-- Skeleton saat mencari / memfilter / pindah halaman --}}
            <div wire:loading.grid wire:target="{{ $loadingTarget }}" aria-hidden="true"
                 class="hidden grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @for ($i = 0; $i < 6; $i++)
                    <div class="animate-pulse rounded border border-slate-200 bg-white p-5 shadow-sm">
                        {{-- badge jenis --}}
                        <div class="h-6 w-24 rounded bg-slate-100"></div>
                        {{-- judul --}}
                        <div class="mt-4 space-y-2">
                            <div class="h-4 w-full rounded bg-slate-200/70"></div>
                            <div class="h-4 w-4/5 rounded bg-slate-200/70"></div>
                        </div>
                        {{-- abstrak --}}
                        <div class="mt-4 space-y-2">
                            <div class="h-3 w-full rounded bg-slate-100"></div>
                            <div class="h-3 w-2/3 rounded bg-slate-100"></div>
                        </div>
                        {{-- meta --}}
                        <div class="mt-6 flex gap-3">
                            <div class="h-3 w-14 rounded bg-slate-100"></div>
                            <div class="h-3 w-20 rounded bg-slate-100"></div>
                            <div class="h-3 w-10 rounded bg-slate-100"></div>
                        </div>
                        {{-- tombol aksi --}}
                        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                            <div class="h-9 rounded bg-slate-100"></div>
                            <div class="h-9 rounded bg-slate-100"></div>
                            <div class="col-span-2 h-9 rounded bg-slate-100"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <div wire:loading.remove wire:target="{{ $loadingTarget }}">
                @if ($disabilitas->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($disabilitas as $doc)
                            <div class="document-card group relative flex h-full cursor-default flex-col overflow-hidden rounded border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-accent/40 hover:shadow-lg hover:shadow-[#012a4d]/10"
                                 data-doc-id="{{ $doc->id }}">
                                <div aria-hidden="true" class="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-linear-to-r from-primary to-accent transition-transform duration-300 group-hover:scale-x-100"></div>

                                <span class="document-type mb-3 inline-flex w-fit items-center gap-1.5 rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">
                                    <i class="fas fa-scale-balanced text-[0.7rem]"></i>{{ $doc->jenis_dokumen_formatted }}
                                </span>
                                <h3 class="document-title mb-2 line-clamp-3 text-base font-semibold leading-snug text-slate-800 transition-colors group-hover:text-accent">{{ $doc->judul }}</h3>
                                <p class="abstract-preview line-clamp-2 text-sm leading-relaxed text-slate-500">
                                    {{ $doc->abstrak ? Str::limit($doc->abstrak, 150) : __('Tidak ada abstrak') }}
                                </p>

                                <div class="document-meta mt-auto flex flex-wrap items-center gap-x-4 gap-y-1 pt-4 text-xs text-slate-500">
                                    <span><i class="far fa-calendar mr-1 text-slate-400"></i> {{ $doc->tahun }}</span>
                                    <span><i class="far fa-file mr-1 text-slate-400"></i> {{ $doc->jumlah_halaman ? $doc->jumlah_halaman . ' ' . __('halaman') : '-' }}</span>
                                    <span><i class="fas fa-file-pdf mr-1 text-slate-400"></i> PDF</span>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                                    <button class="action-btn abstract-btn flex items-center justify-center gap-1.5 rounded bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300"
                                            data-doc-id="{{ $doc->id }}">
                                        <i class="fas fa-file-lines"></i>{{ __('Abstract') }}
                                    </button>
                                    <a href="{{ route('frontend.disabilitas.show', $doc->id) }}" wire:navigate
                                       class="action-btn flex items-center justify-center gap-1.5 rounded bg-accent/10 px-3 py-2 text-sm font-semibold text-accent transition hover:bg-accent hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40">
                                        <i class="fas fa-eye"></i>{{ __('Detail') }}
                                    </a>
                                    @if ($doc->dokumen_utama)
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

                    @if ($disabilitas->hasPages())
                        <div class="mt-10">
                            {{ $disabilitas->links() }}
                        </div>
                    @endif
                @else
                    <div class="rounded border border-dashed border-slate-300 bg-slate-50 px-8 py-14 text-center">
                        <span class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-2xl text-primary">
                            <i class="fas fa-folder-open"></i>
                        </span>
                        <h3 class="mb-1.5 text-lg font-semibold text-slate-700">{{ __('Tidak ada dokumen') }}</h3>
                        <p class="mx-auto max-w-md text-slate-500">{{ __('Coba ubah kata kunci atau atur ulang filter untuk melihat dokumen disabilitas lainnya.') }}</p>
                        @if ($hasFilter)
                            <button type="button" wire:click="resetFilter"
                                    class="mt-5 inline-flex items-center gap-2 rounded bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-hover">
                                <i class="fas fa-rotate-left"></i> {{ __('Reset pencarian') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- /wrapper --}}

    {{-- Modal Abstrak — murni client-side (wire:ignore); buka/tutup via kelas .show (JS) --}}
    <div id="abstractModal" wire:ignore role="dialog" aria-modal="true" aria-labelledby="abstractModalTitle"
        class="abstract-modal fixed inset-0 z-[10000] items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
        <div class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded bg-white shadow-2xl ring-1 ring-slate-900/10">
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

            <div class="flex items-start gap-4 border-b border-slate-200 px-6 pt-6 pb-4 sm:px-8">
                <span aria-hidden="true" class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded bg-primary/10 text-primary">
                    <i class="fas fa-file-lines"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ __('Abstract Dokumen') }}</p>
                    <h3 id="abstractModalTitle" class="mt-0.5 line-clamp-2 text-base font-bold leading-snug text-slate-900 sm:text-lg"></h3>
                </div>
                <button id="closeAbstractModal" type="button" aria-label="{{ __('Tutup') }}"
                    class="-mr-1 flex h-9 w-9 shrink-0 items-center justify-center rounded text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="abstractModalContent" class="overflow-y-auto px-6 py-6 leading-relaxed text-slate-600 sm:px-8"></div>
        </div>
    </div>

    {{-- ============ Voice + Abstract (client-side, delegasi event) ============ --}}
</div>{{-- /root komponen --}}

@script
<script>
    (function () {
        if (window.__disabilitasInit) return;
        window.__disabilitasInit = true;

        // ===== Text-to-speech (baca kartu saat hover bila Mode Suara aktif) =====
        class VoiceSystem {
            constructor() {
                this.enabled = false;
                this.voice = null;
                this.currentCard = null;
                this.hoverTimeout = null;
                this.loadVoices();
            }
            loadVoices() {
                const pick = () => { this.voice = this.bestVoice(speechSynthesis.getVoices()); };
                if (speechSynthesis.getVoices().length) pick();
                else speechSynthesis.onvoiceschanged = pick;
            }
            bestVoice(voices) {
                const rules = [
                    v => v.lang && v.lang.startsWith('id') && /female|perempuan/i.test(v.name),
                    v => v.name.includes('Google') && v.lang.startsWith('id'),
                    v => v.lang && v.lang.startsWith('id'),
                    v => /female/i.test(v.name) && !/male|david|mark/i.test(v.name),
                ];
                for (const r of rules) { const v = voices.find(r); if (v) return v; }
                return voices[0] || null;
            }
            readCard(card) {
                const type = card.querySelector('.document-type')?.textContent.trim() || '';
                const title = card.querySelector('.document-title')?.textContent.trim() || '';
                const abstract = card.querySelector('.abstract-preview')?.textContent.trim() || '';
                const year = card.querySelector('.document-meta span:first-child')?.textContent.trim() || '';
                let text = '';
                if (type) text += `Dokumen ${type}. `;
                if (title) text += `Judul: ${title}. `;
                if (abstract && abstract !== 'Tidak ada abstrak') text += `Abstrak: ${abstract}. `;
                if (year) text += `Tahun ${year}. `;
                text += 'Silakan klik untuk melihat detail.';
                this.speak(text);
            }
            speak(text) {
                if (!this.enabled || !text || !window.speechSynthesis) return;
                this.stop();
                setTimeout(() => {
                    const u = new SpeechSynthesisUtterance(text);
                    u.lang = 'id-ID';
                    if (this.voice) u.voice = this.voice;
                    u.rate = 1.1;   // kecepatan bicara natural (tidak lambat)
                    u.pitch = 1.1;
                    u.volume = 1.0;
                    u.onstart = () => this.currentCard?.classList.add('reading');
                    u.onend = () => this.currentCard?.classList.remove('reading');
                    u.onerror = () => this.currentCard?.classList.remove('reading');
                    window.speechSynthesis.speak(u);
                }, 80);
            }
            stop() {
                window.speechSynthesis?.cancel();
                document.querySelectorAll('.document-card.reading').forEach(c => c.classList.remove('reading'));
            }
            // Samakan tampilan panel + warna tombol dengan status aktif/nonaktif
            syncUI() {
                const status = document.getElementById('voiceStatusText');
                const toggle = document.getElementById('voiceToggle');
                const panel = document.getElementById('voicePanel');
                if (status) { status.textContent = this.enabled ? 'Aktif' : 'Mati'; status.classList.toggle('active', this.enabled); }
                if (toggle) toggle.checked = this.enabled;
                if (panel) panel.classList.toggle('voice-active', this.enabled);
            }
            toggle() {
                this.enabled = !this.enabled;
                this.syncUI();
                if (this.enabled) this.speak('Mode suara diaktifkan. Arahkan kursor ke dokumen untuk mendengarkan deskripsi.');
                else this.stop();
                localStorage.setItem('disabilitasVoice', this.enabled ? '1' : '0');
            }
            restore() {
                this.enabled = localStorage.getItem('disabilitasVoice') === '1';
                this.syncUI();
            }
        }

        // ===== Pencarian suara -> isi input & picu Livewire (tanpa submit form) =====
        class VoiceRecognition {
            constructor() {
                this.listening = false;
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SR) return;
                this.rec = new SR();
                this.rec.lang = 'id-ID';
                this.rec.interimResults = false;
                this.rec.maxAlternatives = 1;
                this.rec.continuous = false;
                this.rec.onresult = (e) => {
                    const input = document.getElementById('mainSearchInput');
                    if (input) {
                        input.value = e.results[0][0].transcript;
                        input.dispatchEvent(new Event('input', { bubbles: true })); // Livewire wire:model.live
                    }
                };
                this.rec.onerror = () => this.stop();
                this.rec.onend = () => this.stop();
            }
            ui(on) {
                const btn = document.getElementById('mainVoiceBtn');
                if (!btn) return;
                btn.innerHTML = on ? '<i class="fas fa-microphone-slash"></i>' : '<i class="fas fa-microphone"></i>';
                btn.classList.toggle('listening', on);
            }
            toggle() {
                if (!this.rec) return;
                if (this.listening) { this.stop(); return; }
                try { this.rec.start(); this.listening = true; this.ui(true); } catch (_) {}
            }
            stop() {
                if (this.rec && this.listening) { try { this.rec.stop(); } catch (_) {} }
                this.listening = false; this.ui(false);
            }
        }

        // ===== Modal abstrak =====
        const modal = {
            el: () => document.getElementById('abstractModal'),
            open: async (docId) => {
                const box = document.getElementById('abstractModalContent');
                const title = document.getElementById('abstractModalTitle');
                title.textContent = 'Memuat abstract…';
                box.innerHTML = `
                    <div class="animate-pulse space-y-6" aria-busy="true">
                        <div class="space-y-2.5">
                            <div class="h-3 w-28 rounded bg-slate-200"></div>
                            <div class="h-3 w-full rounded bg-slate-100"></div>
                            <div class="h-3 w-full rounded bg-slate-100"></div>
                            <div class="h-3 w-3/5 rounded bg-slate-100"></div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="h-16 rounded bg-slate-100"></div>
                            <div class="h-16 rounded bg-slate-100"></div>
                        </div>
                        <div class="h-24 rounded bg-slate-100"></div>
                    </div>`;
                clearTimeout(modal._t);
                modal.el().classList.remove('closing');
                modal.el().classList.add('show');
                try {
                    // route() dipakai agar prefix locale ({locale}) ikut terisi
                    const url = @js(route('frontend.disabilitas.abstract', ['id' => '__ID__'])).replace('__ID__', docId);
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
                    });
                    if (!res.ok) throw new Error('Gagal mengambil abstract');
                    const data = await res.json();
                    const card = document.querySelector(`.document-card[data-doc-id="${docId}"]`);
                    const judul = card?.querySelector('.document-title')?.textContent.trim() || 'Dokumen';
                    const jenis = card?.querySelector('.document-type')?.textContent.trim() || 'Dokumen';
                    title.textContent = judul;
                    const abstrak = (data.abstract || '').trim();

                    const ringkasan = abstrak
                        ? `<p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">${abstrak}</p>`
                        : `<div class="rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center">
                               <i class="fas fa-file-circle-question text-2xl text-slate-300"></i>
                               <p class="mt-2 text-sm font-medium text-slate-500">Abstract belum tersedia</p>
                               <p class="mt-0.5 text-xs text-slate-400">Silakan unduh dokumen untuk membaca isi lengkapnya.</p>
                           </div>`;

                    box.innerHTML = `
                        <section>
                            <h4 class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                <span class="h-3 w-0.5 rounded bg-primary"></span> Ringkasan
                            </h4>
                            <div class="mt-3">${ringkasan}</div>
                        </section>

                        <dl class="mt-6 grid gap-3 border-t border-slate-100 pt-5 sm:grid-cols-2">
                            <div class="rounded bg-slate-50 px-4 py-3">
                                <dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Jenis Dokumen</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-800">${jenis}</dd>
                            </div>
                            <div class="rounded bg-slate-50 px-4 py-3">
                                <dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Judul</dt>
                                <dd class="mt-0.5 text-sm font-semibold leading-snug text-slate-800">${judul}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 flex flex-col gap-3 rounded border border-primary/20 bg-primary/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <span aria-hidden="true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded bg-primary/15 text-primary"><i class="fas fa-volume-up"></i></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Dengarkan Abstract</p>
                                    <p class="text-xs text-slate-500">Pembacaan otomatis untuk pengguna dengan hambatan penglihatan.</p>
                                </div>
                            </div>
                            <button id="playAbstractAudio" type="button" ${abstrak ? '' : 'disabled'}
                                class="flex shrink-0 items-center justify-center gap-2 rounded px-4 py-2.5 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 ${abstrak ? 'bg-primary text-white shadow-sm shadow-primary/25 hover:bg-primary-hover' : 'cursor-not-allowed bg-slate-100 text-slate-400'}">
                                <i class="fas fa-play-circle"></i> Putar Audio
                            </button>
                        </div>`;
                    document.getElementById('playAbstractAudio')?.addEventListener('click', () => {
                        window.voiceSystem?.speak(`Abstract dokumen ${judul}. ${abstrak}`);
                    });
                } catch (err) {
                    title.textContent = 'Gagal memuat';
                    box.innerHTML = `
                        <div class="rounded border border-dashed border-slate-200 bg-slate-50/60 px-4 py-10 text-center">
                            <i class="fas fa-triangle-exclamation text-2xl text-primary"></i>
                            <p class="mt-2 text-sm font-medium text-slate-600">Gagal memuat abstract</p>
                            <p class="mt-0.5 text-xs text-slate-400">Periksa koneksi Anda, lalu coba buka kembali.</p>
                        </div>`;
                }
            },
            close: () => {
                const el = modal.el();
                window.voiceSystem?.stop();
                if (!el || !el.classList.contains('show') || el.classList.contains('closing')) return;
                el.classList.add('closing');
                // ponytail: durasi disamakan dengan @keyframes modalPanelOut (.18s)
                modal._t = setTimeout(() => el.classList.remove('show', 'closing'), 180);
            }
        };

        // ===== Inisialisasi =====
        const voiceSystem = new VoiceSystem();
        const voiceRecognition = new VoiceRecognition();
        window.voiceSystem = voiceSystem;
        voiceSystem.restore();

        // Hover kartu -> baca (delegasi, tahan re-render)
        document.addEventListener('mouseover', (e) => {
            if (!voiceSystem.enabled) return;
            const card = e.target.closest('.document-card');
            if (card && card !== voiceSystem.currentCard) {
                clearTimeout(voiceSystem.hoverTimeout);
                voiceSystem.hoverTimeout = setTimeout(() => { voiceSystem.currentCard = card; voiceSystem.readCard(card); }, 300);
            }
        });
        document.addEventListener('mouseout', (e) => {
            if (e.target.closest('.document-card')) {
                clearTimeout(voiceSystem.hoverTimeout);
                setTimeout(() => { voiceSystem.currentCard = null; }, 100);
            }
        });

        // Klik (delegasi)
        document.addEventListener('click', (e) => {
            if (e.target.closest('#testVoiceBtn')) {
                voiceSystem.enabled ? voiceSystem.speak('Selamat datang di layanan dokumen disabilitas') : alert('Aktifkan mode suara terlebih dahulu');
            }
            if (e.target.closest('#mainVoiceBtn')) voiceRecognition.toggle();
            const ab = e.target.closest('.abstract-btn');
            if (ab) { e.preventDefault(); modal.open(ab.getAttribute('data-doc-id')); }
            if (e.target.closest('#closeAbstractModal')) modal.close();
            if (e.target === modal.el()) modal.close();
        });

        // Perubahan toggle suara (delegasi)
        document.addEventListener('change', (e) => {
            if (e.target && e.target.id === 'voiceToggle') voiceSystem.toggle();
        });

        // Escape: stop suara + tutup modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { voiceSystem.stop(); modal.close(); }
        });
    })();
</script>
@endscript
