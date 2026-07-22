@props(['item', 'delay' => 0])

{{-- Membuka modal video: butuh scope Alpine dari section (vid, vtitle, open) --}}
<button type="button"
    @click="vid = '{{ $item->link }}'; vtitle = @js($item->judul); open = true"
    class="animate-rise group relative block w-full text-left overflow-hidden rounded bg-slate-900/70 ring-1 ring-white/10 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:ring-primary/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
    style="animation-delay: {{ $delay }}s">

    <!-- garis aksen atas saat hover -->
    <span aria-hidden="true" class="absolute inset-x-0 top-0 z-20 h-1 origin-left scale-x-0 bg-primary transition-transform duration-500 group-hover:scale-x-100"></span>

    <!-- Thumbnail -->
    <div class="relative aspect-video overflow-hidden bg-black">
        <img
            src="https://img.youtube.com/vi/{{ $item->link }}/hqdefault.jpg"
            alt="{{ $item->judul }}"
            loading="lazy"
            class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
        <div class="absolute inset-0 bg-black/30 transition-colors duration-300 group-hover:bg-black/20"></div>

        <!-- tombol play -->
        <span aria-hidden="true" class="absolute inset-0 flex items-center justify-center">
            <span class="flex h-14 w-14 items-center justify-center rounded bg-primary/90 text-white shadow-lg transition-transform duration-300 group-hover:scale-110">
                <i class="fas fa-play translate-x-0.5 text-lg"></i>
            </span>
        </span>
    </div>

    <!-- Content -->
    <div class="relative p-5">
        <i class="fas fa-video pointer-events-none absolute -right-1 -top-3 text-4xl text-white/4" aria-hidden="true"></i>

        <h3 class="relative text-xs lg:text-sm font-semibold leading-5 text-white line-clamp-2 transition-colors group-hover:text-primary">
            {{ $item->judul }}
        </h3>

        <div class="relative mt-4 flex items-center justify-between border-t border-white/10 pt-3">
            <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                <i class="fas fa-calendar-day text-slate-500"></i>
                {{ Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wide text-primary">
                <i class="fas fa-play text-[9px]"></i> {{ __('Tonton') }}
            </span>
        </div>
    </div>
</button>
