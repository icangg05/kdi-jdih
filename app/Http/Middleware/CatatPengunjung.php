<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CatatPengunjung
{
    /**
     * Menghitung kunjungan ke tabel `pengunjung` yang sudah ada (warisan situs
     * lama) — tanpa tabel baru. Satu kunjungan = satu sesi: dedup memakai flag
     * di session, jadi refresh berulang tidak menggelembungkan angka.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dicatat setelah respons terbentuk, supaya error/redirect tidak ikut terhitung.
        $response = $next($request);

        if ($this->perluDicatat($request, $response)) {
            $request->session()->put('pengunjung_dicatat', true);
            $this->tambah();
        }

        return $response;
    }

    private function perluDicatat(Request $request, Response $response): bool
    {
        if ($request->session()->get('pengunjung_dicatat')) {
            return false;
        }

        if (! $request->isMethod('GET') || $request->ajax()) {
            return false;
        }

        // Header Livewire menyaring dua hal sekaligus: update komponen (tiap
        // ketikan filter) dan prefetch wire:navigate.hover — request untuk
        // halaman yang belum tentu jadi dibuka pengunjung.
        if ($request->hasHeader('X-Livewire') || $request->hasHeader('X-Livewire-Navigate')) {
            return false;
        }

        if ($request->is('livewire/*', 'dashboard/*', 'login', 'up')) {
            return false;
        }

        if ($this->bot($request->userAgent())) {
            return false;
        }

        // Hanya halaman HTML yang sukses — menyaring unduhan PDF & respons JSON.
        return $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    /**
     * Crawler & klien non-manusia. Perayapan mesin pencari bisa menaikkan angka
     * ratusan dalam sekejap karena tiap request-nya tanpa cookie — selalu
     * dianggap pengunjung baru.
     */
    private function bot(?string $userAgent): bool
    {
        // UA kosong hampir selalu skrip/pemantau, bukan peramban.
        if (blank($userAgent)) {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|curl|wget|python|java|go-http|axios|okhttp|headless|phantom|monitor|uptime|preview|fetch|scrape|libwww|httpclient|postman|insomnia|lighthouse|pingdom|semrush|ahrefs|facebookexternalhit|whatsapp|telegram|embedly/i',
            $userAgent
        );
    }

    private function tambah(): void
    {
        $hari  = now()->toDateString();
        $bulan = now()->format('Y-m');

        try {
            // Satu UPDATE atomik: rollover harian/bulanan sekaligus penambahan,
            // tanpa SELECT dulu sehingga bebas race condition.
            //
            // PENTING: `hari` dan `bulan` harus di-assign PALING AKHIR. MySQL
            // mengevaluasi assignment dari kiri ke kanan dan ekspresi di kanan
            // melihat nilai BARU kolom yang sudah di-assign sebelumnya — kalau
            // dipindah ke atas, IF() selalu bernilai benar dan hitungan harian
            // tidak akan pernah kembali ke 1 saat berganti hari.
            DB::update("
                UPDATE pengunjung SET
                    jumlah_perhari     = IF(hari = ?, jumlah_perhari + 1, 1),
                    jumlah_bulan       = IF(bulan = ?, jumlah_bulan + 1, 1),
                    jumlah_keseluruhan = jumlah_keseluruhan + 1,
                    hari  = ?,
                    bulan = ?
                WHERE id = 1
            ", [$hari, $bulan, $hari, $bulan]);
        } catch (\Throwable $e) {
            // Statistik footer tidak boleh menjatuhkan halaman.
            report($e);
        }
    }
}
