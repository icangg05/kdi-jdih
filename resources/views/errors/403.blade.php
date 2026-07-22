@extends('errors.layout')

@section('kode', '403')
@section('ikon', 'fa-lock')
@section('judul', __('Akses ditolak'))
@section('pesan', __('Anda tidak memiliki izin untuk membuka halaman ini. Bila seharusnya punya akses, masuk lebih dulu dengan akun yang benar.'))

@section('aksi')
    <a href="{{ url('/backend') }}"
       class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
        <i class="fas fa-right-to-bracket"></i>
        {{ __('Masuk ke Dashboard') }}
    </a>
@endsection
