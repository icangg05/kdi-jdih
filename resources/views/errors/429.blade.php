@extends('errors.layout')

@section('kode', '429')
@section('ikon', 'fa-gauge-high')
@section('judul', __('Terlalu banyak permintaan'))
@section('pesan', __('Permintaan dari perangkat Anda masuk terlalu cepat dan sementara kami tahan. Tunggu sebentar, lalu coba lagi.'))

@section('aksi')
    <button type="button" id="tombolCoba" onclick="location.reload()"
            class="inline-flex items-center justify-center gap-2.5 rounded bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition hover:bg-primary-hover active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 focus-visible:ring-offset-darkbg">
        <i class="fas fa-arrow-rotate-right"></i>
        <span id="labelCoba">{{ __('Coba Lagi') }}</span>
    </button>

    <script>
        // Tombol dikunci selama hitungan mundur supaya tidak menambah beban
        (function () {
            var sisa = 15;
            var btn = document.getElementById('tombolCoba');
            var label = document.getElementById('labelCoba');
            var teks = @js(__('Coba Lagi'));

            btn.disabled = true;
            label.textContent = teks + ' (' + sisa + ')';

            var timer = setInterval(function () {
                sisa -= 1;
                label.textContent = sisa > 0 ? teks + ' (' + sisa + ')' : teks;
                if (sisa <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                }
            }, 1000);
        })();
    </script>
@endsection
