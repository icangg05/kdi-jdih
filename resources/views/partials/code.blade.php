{{-- Blok kode dengan label + tombol salin. Props: $label, $code --}}
<div data-code-block class="overflow-hidden rounded-lg ring-1 ring-slate-700/50">
    <div class="flex items-center justify-between bg-slate-800 px-4 py-2">
        <span class="font-mono text-xs font-medium text-slate-400">{{ $label ?? 'code' }}</span>
        <button type="button" data-copy class="rounded px-2 py-1 text-xs font-medium text-slate-300 transition hover:bg-slate-700 hover:text-white">{{ __('Salin') }}</button>
    </div>
    <pre class="overflow-x-auto bg-slate-900 p-4 text-sm leading-relaxed text-slate-100"><code>{{ $code }}</code></pre>
</div>
