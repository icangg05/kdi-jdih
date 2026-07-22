@extends('errors.layout')

@section('kode', '404')
@section('ikon', 'fa-file-circle-question')
@section('judul', __('Halaman tidak ditemukan'))
@section('pesan', __('Alamat yang Anda buka sudah dipindahkan, salah ketik, atau dokumennya tidak lagi tersedia di JDIH Kota Kendari.'))

@section('bantuan')
    <form method="GET" action="{{ url(app()->getLocale() . '/dokumen/peraturan') }}"
          class="rounded border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
        <label for="q" class="text-xs font-semibold uppercase tracking-wider text-white/50">
            {{ __('Coba cari dokumennya di sini') }}
        </label>
        <div class="mt-2.5 flex flex-col gap-2 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="q" name="q" autocomplete="off" autofocus
                       placeholder="{{ __('Contoh: retribusi parkir, izin mendirikan bangunan') }}"
                       class="w-full rounded bg-white py-3 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-500 outline-none ring-2 ring-transparent transition focus:ring-primary">
            </div>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-accent-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
                <i class="fas fa-magnifying-glass"></i>
                {{ __('Cari') }}
            </button>
        </div>
    </form>
@endsection
