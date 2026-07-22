{{-- Cadangan untuk seluruh galat 5xx yang tidak punya halaman khusus (502, 504, ...) --}}
@extends('errors.layout')

@section('kode', ($exception ?? null)?->getStatusCode() ?? '500')
@section('ikon', 'fa-screwdriver-wrench')
@section('judul', __('Layanan sedang tidak dapat diakses'))
@section('pesan', __('Server tidak dapat menyelesaikan permintaan Anda saat ini. Kami sedang menanganinya — silakan coba lagi beberapa saat lagi.'))

@section('aksi')
    <button type="button" onclick="location.reload()"
            class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
        <i class="fas fa-arrow-rotate-right"></i>
        {{ __('Coba Lagi') }}
    </button>
@endsection
