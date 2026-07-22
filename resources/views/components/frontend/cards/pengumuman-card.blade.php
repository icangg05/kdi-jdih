@props([
    'item',
    'index' => null,
    'delay' => 0,
    'route' => 'frontend.pengumuman.show',
    'badge' => null,
    'badgeIcon' => 'fas fa-thumbtack',
])

@php
    $image = checkFilePath(config('app.img_directory'), $item->image)
        ? asset('storage/' . config('app.img_directory') . $item->image)
        : asset('assets/img/default-img.jpg');
    $badgeLabel = $badge ?? __('Pemberitahuan Putusan');
@endphp

<a href="{{ route($route, Hashids::encode($item->id)) }}" wire:navigate.hover
    class="animate-rise group relative block overflow-hidden rounded bg-white ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-primary/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2"
    style="animation-delay: {{ $delay }}s">

    <!-- garis aksen atas saat hover -->
    <span aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

    <div class="flex flex-col md:flex-row">

        <!-- Image -->
        <div class="relative md:w-72 shrink-0 overflow-hidden">
            <img
                src="{{ $image }}"
                alt="{{ Str::limit(tt($item, 'judul'), 60) }}"
                class="h-48 md:h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <div class="absolute inset-0 bg-linear-to-t from-black/50 via-black/10 to-transparent md:hidden"></div>

            @if (!is_null($index))
                <!-- nomor indeks -->
                <span class="absolute left-4 top-4 flex h-10 w-10 items-center justify-center rounded bg-primary text-sm font-bold text-white shadow-lg tabular-nums">
                    {{ str_pad($index, 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </div>

        <!-- Content -->
        <div class="relative flex flex-1 flex-col justify-between p-6 lg:p-8">

            <!-- watermark -->
            <i class="{{ $badgeIcon }} pointer-events-none absolute -right-2 -top-1 text-5xl text-slate-900/3 -rotate-12" aria-hidden="true"></i>

            <div class="relative">
                <span class="inline-flex items-center gap-1.5 mb-4 rounded bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                    <i class="{{ $badgeIcon }}"></i>
                    {{ $badgeLabel }}
                </span>

                <h3 class="text-base lg:text-lg font-semibold leading-snug text-slate-900 transition group-hover:text-primary">
                    {{ Str::limit(tt($item, 'judul'), 60) }}
                </h3>

                <p class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-2">
                    {{ Str::limit(strip_tags(tt($item, 'isi')), 130) }}
                </p>
            </div>

            <div class="relative mt-6 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <span class="inline-flex items-center gap-1.5 text-xs lg:text-sm text-slate-500">
                    <i class="fas fa-calendar-day text-slate-400"></i>
                    {{ Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                </span>

                <span class="inline-flex items-center gap-1 text-xs lg:text-sm font-semibold text-primary transition-all group-hover:gap-2">
                    {{ __('Baca Selengkapnya') }}
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </span>
            </div>
        </div>
    </div>
</a>
