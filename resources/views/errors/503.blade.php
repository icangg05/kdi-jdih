@extends('errors.layout')

@section('kode', '503')
@section('ikon', 'fa-screwdriver-wrench')
@section('judul', __('Situs sedang dalam pemeliharaan'))
@section('pesan', __('Kami sedang memperbarui layanan JDIH Kota Kendari agar lebih baik. Halaman akan kembali normal dalam waktu dekat.'))

@section('aksi')
    <button type="button" onclick="location.reload()"
            class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
        <i class="fas fa-arrow-rotate-right"></i>
        {{ __('Periksa Lagi') }}
    </button>
@endsection
