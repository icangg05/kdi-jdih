@php
    $currentLocale = app()->getLocale();
    $languages = [
        'id' => ['name' => 'Indonesia', 'flag' => '🇮🇩'],
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'zh' => ['name' => '中文', 'flag' => '🇨🇳'],
        'ko' => ['name' => '한국어', 'flag' => '🇰🇷'],
    ];
@endphp
<div x-data="{ open: false }" class="relative no-print">
    <button @click="open = !open" @click.away="open = false"
        class="flex h-9 w-9 items-center justify-center rounded text-white/80 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        title="{{ __('Pilih bahasa') }}" aria-label="{{ __('Pilih bahasa') }}" :aria-expanded="open.toString()">
        <span class="text-base leading-none">{{ $languages[$currentLocale]['flag'] ?? '🌐' }}</span>
    </button>
    <div x-show="open" x-transition x-cloak
        class="absolute bottom-0 left-full ml-3 z-50 min-w-40 rounded border border-slate-200 bg-white py-1 shadow-xl">
        @foreach ($languages as $code => $lang)
            @php
                $segments = request()->segments();
                if (count($segments) > 0 && array_key_exists($segments[0], $languages)) {
                    $segments[0] = $code;
                } else {
                    array_unshift($segments, $code);
                }
                $url = url(implode('/', $segments));
                if (request()->getQueryString()) {
                    $url .= '?' . request()->getQueryString();
                }
            @endphp
            <a href="{{ $url }}"
                class="flex items-center gap-3 px-4 py-2 text-sm transition-colors {{ $currentLocale === $code ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="text-lg">{{ $lang['flag'] }}</span>
                <span>{{ $lang['name'] }}</span>
                @if ($currentLocale === $code)
                    <i class="fas fa-check ml-auto text-primary text-xs"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>
