@extends('errors.layout')

@section('kode', '419')
@section('ikon', 'fa-hourglass-end')
@section('judul', __('Sesi Anda kedaluwarsa'))
@section('pesan', __('Halaman dibiarkan terbuka terlalu lama sehingga token keamanannya tidak lagi berlaku. Muat ulang halaman, lalu kirim kembali isian Anda.'))

@section('aksi')
    <button type="button" onclick="location.reload()"
            class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
        <i class="fas fa-arrow-rotate-right"></i>
        {{ __('Muat Ulang Halaman') }}
    </button>
@endsection

@section('bantuan')
    <p class="flex items-start gap-2.5 rounded border border-white/10 bg-white/5 p-4 text-sm text-slate-300 backdrop-blur-sm">
        <i class="fas fa-circle-info mt-0.5 text-primary"></i>
        {{ __('Tips: salin dulu teks panjang yang sudah Anda tulis sebelum memuat ulang, agar tidak perlu mengetik dari awal.') }}
    </p>
@endsection
