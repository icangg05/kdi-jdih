{{-- Cadangan untuk seluruh galat 4xx yang tidak punya halaman khusus (400, 405, 408, 422, ...) --}}
@extends('errors.layout')

@section('kode', ($exception ?? null)?->getStatusCode() ?? '400')
@section('ikon', 'fa-circle-exclamation')
@section('judul', __('Permintaan tidak dapat diproses'))
@section('pesan', __('Alamat atau permintaan yang dikirim tidak dikenali oleh server. Periksa kembali tautannya, atau mulai dari beranda.'))
