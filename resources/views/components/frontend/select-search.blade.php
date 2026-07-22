@props([
    'name',
    'label' => null,
    'placeholder' => 'Pilih',
    'icon' => 'fa-solid fa-list',
    'options' => [],
    'searchPlaceholder' => 'Cari...',
    'wire' => true,
    'inputId' => null,
])

{{--
    Select dengan pencarian, reusable.

    Dua mode:
    - wire=true (default): binding Livewire lewat @entangle($name).live,
      auto-apply saat opsi dipilih.
    - wire=false: tanpa Livewire. Nilainya ditulis ke <input type="hidden">
      ber-name/id, sehingga form biasa & JS (.value, form.reset()) tetap jalan.

    $options: array of ['value' => ..., 'label' => ...]
--}}
<div class="space-y-1.5">
    @if ($label)
        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</label>
    @endif

    <div class="relative"
        @unless ($wire) @reset.window="model = ''" @endunless
        x-data="{
            open: false,
            search: '',
            model: @if ($wire) @entangle($name).live @else '' @endif,
            options: {{ \Illuminate\Support\Js::from($options) }},
            get filtered() {
                if (!this.search) return this.options;
                const s = this.search.toLowerCase();
                return this.options.filter(o => String(o.label).toLowerCase().includes(s));
            },
            get selectedLabel() {
                const f = this.options.find(o => String(o.value) === String(this.model));
                return f ? f.label : '';
            },
            choose(v) { this.model = v; this.open = false; this.search = ''; },
            clear() { this.model = ''; this.search = ''; },
        }"
        @click.outside="open = false"
        @keydown.escape="open = false"
        x-init="$watch('open', v => { if (v) $nextTick(() => $refs.q && $refs.q.focus()) })">

        @unless ($wire)
            {{-- Pembawa nilai untuk form/JS non-Livewire --}}
            <input type="hidden" name="{{ $name }}" id="{{ $inputId ?? $name }}" x-model="model">
        @endunless

        {{-- Ikon kiri --}}
        <i class="{{ $icon }} pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm z-10"></i>

        {{-- Trigger --}}
        <button type="button" @click="open = !open"
            class="w-full flex items-center pl-11 pr-9 py-2.5 rounded border bg-white text-left text-sm transition focus:outline-none"
            :class="open ? 'border-accent ring-4 ring-accent/10' : 'border-gray-300'">
            <span class="truncate" x-show="selectedLabel" x-text="selectedLabel" :class="'text-slate-800'"></span>
            <span class="truncate text-gray-400" x-show="!selectedLabel">{{ $placeholder }}</span>
        </button>

        {{-- Chevron / tombol clear --}}
        <i class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs transition-transform"
            x-show="!selectedLabel" :class="open && 'rotate-180'"></i>
        <button type="button" @click="clear()" x-show="selectedLabel" x-cloak
            class="absolute right-3 top-1/2 -translate-y-1/2 flex h-5 w-5 items-center justify-center rounded text-gray-400 hover:text-primary hover:bg-primary/10 transition">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>

        {{-- Panel --}}
        <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="absolute z-30 mt-2 w-full rounded border border-gray-200 bg-white shadow-xl shadow-black/5 overflow-hidden">

            <div class="p-2 border-b border-gray-100">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-ref="q" x-model="search"
                        @keydown.enter.prevent="filtered.length && choose(filtered[0].value)"
                        placeholder="{{ $searchPlaceholder }}"
                        class="w-full pl-8 pr-3 py-2 rounded border border-gray-200 text-sm transition focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10">
                </div>
            </div>

            <ul class="max-h-56 overflow-y-auto py-1 text-sm">
                <template x-for="opt in filtered" :key="opt.value">
                    <li>
                        <button type="button" @click="choose(opt.value)"
                            class="w-full text-left px-4 py-2 flex items-center justify-between gap-2 transition-colors hover:bg-accent/5"
                            :class="String(model) === String(opt.value) ? 'text-primary font-semibold bg-primary/5' : 'text-slate-700'">
                            <span class="truncate" x-text="opt.label"></span>
                            <i class="fa-solid fa-check text-primary text-xs" x-show="String(model) === String(opt.value)"></i>
                        </button>
                    </li>
                </template>
                <li x-show="filtered.length === 0" class="px-4 py-3 text-center text-xs text-gray-400">
                    {{ __('Tidak ada pilihan') }}
                </li>
            </ul>
        </div>
    </div>
</div>
