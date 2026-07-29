<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class AiSearchController extends Controller
{
    /** tipe_dokumen -> segmen kategori pada route frontend.dokumen.show */
    private const KATEGORI = [1 => 'peraturan', 2 => 'monografi', 3 => 'artikel', 4 => 'putusan'];

    /** Penanda "Gemini sedang tak bisa dihubungi, jangan buang waktu dulu". */
    private const JEDA_KEY = 'gemini:jeda';
    private const GAGAL_KEY = 'gemini:gagal';
    private const JEDA_MENIT = 2;
    /** Satu kegagalan masih wajar (jalurnya ~7 dari 10 berhasil); dua berturut-turut baru berarti padam. */
    private const BATAS_GAGAL = 2;

    public function search(Request $request)
    {
        $data = $request->validate([
            'query'          => 'required|string|min:1|max:500',
            // Riwayat percakapan dikirim ulang oleh klien tiap giliran (tanpa state di server)
            'history'        => 'sometimes|array|max:20',
            'history.*.role' => 'required_with:history|in:user,ai',
            'history.*.text' => 'required_with:history|string|max:4000',
        ]);

        $query = $data['query'];

        // Pertanyaan susulan yang sangat pendek ("kenapa?") tidak layak dijadikan
        // kata kunci pencarian — LIKE-nya akan mencocokkan hampir semua dokumen.
        $documents = mb_strlen($query) >= 3 ? $this->searchDocuments($query) : collect();

        return response()->json([
            'query'       => $query,
            'explanation' => $this->explain($query, $documents, $data['history'] ?? []),
            'documents'   => $documents,
            'total'       => $documents->count(),
        ]);
    }

    /**
     * Cari di tabel `document` (kolom nyata: judul, abstrak, bentuk_peraturan, ...)
     * lalu ranking sederhana berdasarkan kata yang cocok di judul.
     */
    private function searchDocuments(string $query)
    {
        $words = array_values(array_filter(
            preg_split('/\s+/', mb_strtolower($query)),
            fn ($w) => mb_strlen($w) > 2 && !in_array($w, ['yang', 'dan', 'atau', 'untuk', 'tentang', 'apa', 'itu', 'dengan', 'pada'])
        ));
        $terms = $words ?: [mb_strtolower($query)];

        return DB::table('document')
            ->where(function ($q) use ($terms) {
                foreach ($terms as $w) {
                    $q->orWhere('judul', 'like', "%{$w}%")
                        ->orWhere('abstrak', 'like', "%{$w}%")
                        ->orWhere('bentuk_peraturan', 'like', "%{$w}%");
                }
            })
            ->limit(200)
            ->get(['id', 'tipe_dokumen', 'judul', 'abstrak', 'bentuk_peraturan', 'jenis_peraturan', 'nomor_peraturan', 'tahun_terbit', 'status'])
            ->map(function ($d) use ($query, $terms) {
                $judul = mb_strtolower($d->judul ?? '');
                $hit   = count(array_filter($terms, fn ($w) => str_contains($judul, $w)));

                // skor: proporsi kata yang cocok di judul + bonus frasa utuh
                $score = (int) round(($hit / max(count($terms), 1)) * 80);
                if (str_contains($judul, mb_strtolower($query))) {
                    $score += 20;
                }

                $kategori = self::KATEGORI[$d->tipe_dokumen] ?? 'peraturan';

                return [
                    'id'          => $d->id,
                    'title'       => $d->judul,
                    'type'        => $d->bentuk_peraturan ?: ($d->jenis_peraturan ?: 'Dokumen Hukum'),
                    'year'        => $d->tahun_terbit,
                    'number'      => $d->nomor_peraturan,
                    'status'      => $d->status,
                    'description' => Str::limit(trim(strip_tags((string) $d->abstrak)), 220) ?: null,
                    'category'    => $kategori, // untuk navigasi detail di aplikasi mobile
                    'url'         => route('frontend.dokumen.show', ['locale' => app()->getLocale(), 'kategori' => $kategori, 'id' => Hashids::encode($d->id)]),
                    'accuracy'    => max(10, min(100, $score)),
                ];
            })
            ->sortByDesc('accuracy')
            ->values()
            ->take((int) config('services.ai_search.max_results', 8));
    }

    /**
     * Jawaban AI, dengan riwayat percakapan agar pertanyaan susulan nyambung.
     * Gagal / tidak dikonfigurasi -> ringkasan buatan sendiri.
     */
    private function explain(string $query, $documents, array $history = []): string
    {
        $apiKey = config('services.google_gemini.api_key');

        // Jangan cek format kunci di sini — Google memakai lebih dari satu format
        // (AIza… dan AQ.…), dan kunci yang ditolak akan ketahuan dari respons API.
        if (!config('services.ai_search.enabled', true) || empty($apiKey)) {
            return $this->fallbackExplanation($query, $documents);
        }

        // Jaringan ke Google mati-hidup dalam periode panjang (teramati belasan
        // menit berturut-turut, bukan gagal acak per koneksi). Selama periode
        // buruk, setiap pertanyaan membayar timeout penuh untuk hasil yang sama,
        // jadi setelah satu kegagalan Gemini dilewati dulu — pertanyaan pertama
        // setelah jeda itu yang jadi penjajak apakah jaringan sudah pulih.
        if (Cache::get(self::JEDA_KEY)) {
            return $this->fallbackExplanation($query, $documents);
        }

        $konteks = $documents->take(6)
            ->map(fn ($d) => "- {$d['title']} (" . trim("{$d['type']} {$d['year']}") . ", status: " . ($d['status'] ?: 'tidak tercatat') . ')')
            ->implode("\n") ?: '(tidak ada dokumen yang cocok di database)';

        $aturan = "Anda asisten AI JDIH (Jaringan Dokumentasi dan Informasi Hukum) Kota Kendari. "
            . "Anda mengobrol dengan pengguna, jadi jawab dengan ramah dan mengalir, "
            . "serta ingat isi percakapan sebelumnya.\n"
            . "Aturan:\n"
            . "- Langsung ke isi jawaban. Jangan membuka dengan sapaan atau seruan "
            . "(\"Halo\", \"Wah\", \"Tentu\", \"Baik\", \"Pertanyaan bagus\") — sapaan di tiap "
            . "giliran membuat percakapan terasa kaku, bukan ramah.\n"
            . "- Ramah itu dari pilihan kata yang wajar dan penjelasan yang jelas, "
            . "bukan dari basa-basi. Hindari juga penutup berbunga-bunga.\n"
            . "- Jawab memakai bahasa yang dipakai pengguna bertanya.\n"
            . "- Bila pertanyaannya soal peraturan Kota Kendari, rujuk dokumen di bawah dan sebut judulnya.\n"
            . "- Pertanyaan umum (konsep hukum, tata cara, atau obrolan biasa) tetap dijawab dari pengetahuan Anda, "
            . "namun beri tahu bahwa itu penjelasan umum, bukan kutipan dokumen JDIH.\n"
            . "- Jangan pernah mengarang nomor, tahun, atau isi peraturan.\n"
            . "- Panjang secukupnya (2-6 kalimat). Boleh memakai daftar '- ' dan **tebal** bila membantu.\n"
            . "- Untuk hal yang butuh kepastian hukum, sarankan memeriksa dokumen aslinya.";

        // Giliran lama dikirim apa adanya; aturan + konteks dokumen ditempel di
        // giliran terbaru supaya tetap jalan di model yang belum punya systemInstruction.
        $contents = [];
        foreach (array_slice($history, -8) as $h) {
            $contents[] = [
                'role'  => $h['role'] === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $h['text']]],
            ];
        }
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => "{$aturan}\n\nDokumen JDIH Kota Kendari yang cocok dengan pertanyaan ini:\n{$konteks}\n\nPertanyaan pengguna: \"{$query}\""]],
        ];

        $model = config('services.google_gemini.model', 'gemini-flash-lite-latest');
        $path  = "/v1beta/models/{$model}:generateContent";
        $body  = [
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => (float) config('services.google_gemini.temperature', 0.4),
                'maxOutputTokens' => (int) config('services.google_gemini.max_tokens', 512),
            ],
        ];

        // Jaringan ke Google (dan sebagian edge Cloudflare) men-drop respons POST:
        // TCP+TLS beres, request terkirim, respons tidak pernah datang. Dicoba
        // lewat jalur langsung DAN relay Cloudflare Worker (bila dikonfigurasi).
        // Kunci lewat header, bukan query string: URL ikut tertulis ke log saat
        // error (kunci lama sempat dicabut Google karena "reported as leaked").
        $timeout = (int) config('services.google_gemini.timeout', 15);
        $relay   = rtrim((string) config('services.google_gemini.relay_url'), '/');

        // Relay lebih dulu, bukan berbarengan: terukur ~7 dari 10 berhasil
        // melawan ~1 dari 10 lewat jalur langsung, dan menjalankan keduanya
        // sekaligus (Http::pool) memaksa menunggu yang paling lambat — jawaban
        // relay yang tiba di detik ke-2 jadi tertahan sampai jalur langsung
        // habis waktunya. Jalur langsung tetap dicoba kalau relay gagal.
        $targets = [];
        if ($relay) {
            $targets[] = [$relay . $path, [
                'x-goog-api-key' => $apiKey,
                'x-relay-token'  => (string) config('services.google_gemini.relay_token'),
            ]];
        }
        $targets[] = ['https://generativelanguage.googleapis.com' . $path, ['x-goog-api-key' => $apiKey]];

        // Percobaan harus selesai sebelum PHP membunuh request-nya
        // (max_execution_time, umumnya 30 detik) — kalau kelewat, yang sampai ke
        // pengguna bukan fallback yang rapi tapi HTTP 500. Sisakan 5 detik untuk
        // merangkai jawaban + mengirim respons.
        $mulai = microtime(true);
        $batas = (int) ini_get('max_execution_time') ?: 30;
        $sisa  = fn () => $batas - 5 - (microtime(true) - $mulai);

        foreach ($targets as [$url, $headers]) {
            // Percobaan yang tidak muat lagi hanya membakar sisa waktu.
            if ($sisa() < 3) {
                break;
            }

            try {
                $res = Http::timeout((int) min($timeout, $sisa()))->withHeaders($headers)->post($url, $body);

                $text = trim((string) data_get($res->json(), 'candidates.0.content.parts.0.text'));
                if ($res->successful() && $text !== '') {
                    Cache::forget(self::GAGAL_KEY);

                    return $text;
                }

                Log::warning('Gemini gagal', ['status' => $res->status(), 'body' => Str::limit($res->body(), 300)]);
            } catch (\Throwable $e) {
                Log::warning('Gemini error', ['url' => parse_url($url, PHP_URL_HOST), 'error' => $e->getMessage()]);
            }
        }

        // Semua jalur gagal. Kegagalan sesekali wajar, jadi jangan langsung
        // mematikan AI — tapi begitu dua permintaan beruntun gagal, jaringannya
        // memang sedang padam dan penanya berikutnya tidak perlu ikut menunggu
        // timeout yang sama. Pertanyaan pertama setelah jeda jadi penjajaknya.
        // add() dulu: increment() pada kunci yang belum ada mengembalikan false
        // di driver database. TTL-nya sekalian membuat kegagalan lama kedaluwarsa.
        Cache::add(self::GAGAL_KEY, 0, now()->addMinutes(5));

        if ((int) Cache::increment(self::GAGAL_KEY) >= self::BATAS_GAGAL) {
            Cache::put(self::JEDA_KEY, true, now()->addMinutes(self::JEDA_MENIT));
            Cache::forget(self::GAGAL_KEY);
        }

        return $this->fallbackExplanation($query, $documents);
    }

    /**
     * Dipakai saat layanan AI mati. Bukan jawaban model, tapi tetap menjelaskan
     * dulu — dirangkai dari abstrak dokumen teratas, bukan sekadar mencacah hasil.
     */
    private function fallbackExplanation(string $query, $documents): string
    {
        $catatan = "\nCatatan: layanan AI sedang tidak dapat dihubungi, jadi penjelasan ini disusun otomatis dari data dokumen.";

        if ($documents->isEmpty()) {
            return "Saya belum menemukan dokumen yang cocok dengan \"{$query}\" di JDIH Kota Kendari. "
                . "Coba kata kunci yang lebih umum, misalnya nama bidangnya (\"retribusi\", \"izin\", \"pajak\") "
                . "atau langsung nomor peraturannya."
                . $catatan;
        }

        $utama = $documents->first();
        $lain  = $documents->count() - 1;

        $teks = "Untuk \"{$query}\", dokumen yang paling mendekati di JDIH Kota Kendari adalah **{$utama['title']}**"
            . ($utama['status'] ? " dengan status {$utama['status']}" : '') . ".\n";

        if ($utama['description']) {
            $teks .= "\nRingkasan dokumen tersebut: {$utama['description']}\n";
        }

        if ($lain > 0) {
            $jenis = $documents->pluck('type')->unique()->filter()->take(3)->implode(', ');
            $teks .= "\nAda {$lain} dokumen lain yang juga terkait" . ($jenis ? ", berupa {$jenis}" : '')
                . ". Semuanya tercantum di bawah, diurutkan dari yang paling mirip dengan pertanyaan Anda.\n";
        }

        return $teks . $catatan;
    }
}
