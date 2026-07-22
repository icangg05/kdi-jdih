@props(['item', 'delay' => 0])

@php
    $image = checkFilePath(config('app.img_directory'), $item->image)
        ? asset('storage/' . config('app.img_directory') . $item->image)
        : asset('assets/img/default-img.jpg');
@endphp

<a href="{{ route('frontend.berita.show', Hashids::encode($item->id)) }}" wire:navigate.hover
    class="animate-rise group relative flex flex-col overflow-hidden rounded bg-white ring-1 ring-slate-200 transform-gpu will-change-transform transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-2xl hover:ring-primary/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2"
    style="animation-delay: {{ $delay }}s">

    <!-- garis aksen atas saat hover -->
    <span aria-hidden="true" class="absolute inset-x-0 top-0 z-20 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

    <!-- Image -->
    <div class="relative h-56 overflow-hidden">
        <img
            src="{{ $image }}"
            alt="Berita"
            class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />

        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/10 to-transparent"></div>

        <!-- Date badge -->
        <span class="absolute left-4 top-4 z-10 inline-flex items-center gap-1.5 rounded bg-white/95 px-3 py-1 text-xs font-semibold text-slate-700 shadow tabular-nums">
            <i class="fas fa-calendar-day text-[10px] text-primary"></i>
            {{ Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
        </span>

        <!-- ikon baca saat hover -->
        <span aria-hidden="true" class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            <span class="flex h-12 w-12 items-center justify-center rounded bg-white/90 text-primary shadow-lg scale-90 transition-transform duration-300 group-hover:scale-100">
                <i class="fas fa-arrow-right"></i>
            </span>
        </span>
    </div>

    <!-- Content -->
    <div class="relative flex flex-1 flex-col p-6">
        <h3 class="text-base lg:text-lg font-semibold leading-snug text-slate-800 line-clamp-2 transition group-hover:text-primary">
            {{ Str::limit(tt($item, 'judul'), 20) }}
        </h3>

        <p class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
            {{ Str::limit(strip_tags(tt($item, 'isi')), 100) }}
        </p>

        <!-- footer menempel bawah -->
        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
            <span class="inline-flex items-center gap-1 text-sm font-semibold text-primary transition-all group-hover:gap-2">
                {{ __('Baca Selengkapnya') }}
                <i class="fas fa-arrow-right text-[10px]"></i>
            </span>
            <i class="fas fa-newspaper text-slate-300" aria-hidden="true"></i>
        </div>
    </div>
</a>
