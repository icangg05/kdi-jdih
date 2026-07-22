<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class AiSearchController extends Controller
{
    /** tipe_dokumen -> segmen kategori pada route frontend.dokumen.show */
    private const KATEGORI = [1 => 'peraturan', 2 => 'monografi', 3 => 'artikel', 4 => 'putusan'];

    public function search(Request $request)
    {
        $query = $request->validate([
            'query' => 'required|string|min:3|max:255',
        ])['query'];

        $documents = $this->searchDocuments($query);

        return response()->json([
            'query'       => $query,
            'explanation' => $this->explain($query, $documents),
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
     * Jawaban AI. Gagal / tidak dikonfigurasi -> ringkasan buatan sendiri.
     */
    private function explain(string $query, $documents): string
    {
        $apiKey = config('services.google_gemini.api_key');

        if (!config('services.ai_search.enabled', true) || empty($apiKey) || !str_starts_with((string) $apiKey, 'AI')) {
            return $this->fallbackExplanation($query, $documents);
        }

        $konteks = $documents->take(6)
            ->map(fn ($d) => "- {$d['title']} (" . trim("{$d['type']} {$d['year']}") . ", status: " . ($d['status'] ?: 'tidak tercatat') . ')')
            ->implode("\n") ?: '(tidak ada dokumen yang cocok di database)';

        $prompt = "Anda asisten JDIH (Jaringan Dokumentasi dan Informasi Hukum) Kota Kendari.\n"
            . "Pertanyaan pengguna: \"{$query}\"\n\n"
            . "Dokumen dari database JDIH Kota Kendari:\n{$konteks}\n\n"
            . "Jawab dalam Bahasa Indonesia yang jelas untuk masyarakat umum, maksimal 4 kalimat. "
            . "Rujuk dokumen di atas bila relevan. Jangan mengarang nomor atau isi peraturan yang tidak tercantum. "
            . "Bila tidak ada dokumen yang cocok, katakan demikian dan sarankan kata kunci lain. Tulis teks biasa tanpa markdown.";

        try {
            $model = config('services.google_gemini.model', 'gemini-flash-lite-latest');

            $res = Http::timeout((int) config('services.google_gemini.timeout', 15))
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature'     => (float) config('services.google_gemini.temperature', 0.4),
                        'maxOutputTokens' => (int) config('services.google_gemini.max_tokens', 512),
                    ],
                ]);

            $text = trim((string) data_get($res->json(), 'candidates.0.content.parts.0.text'));

            if ($res->successful() && $text !== '') {
                return $text;
            }

            Log::warning('Gemini gagal', ['status' => $res->status(), 'body' => Str::limit($res->body(), 300)]);
        } catch (\Throwable $e) {
            Log::warning('Gemini error', ['error' => $e->getMessage()]);
        }

        return $this->fallbackExplanation($query, $documents);
    }

    private function fallbackExplanation(string $query, $documents): string
    {
        if ($documents->isEmpty()) {
            return "Tidak ditemukan dokumen yang cocok dengan \"{$query}\" pada database JDIH Kota Kendari. Coba kata kunci yang lebih umum, misalnya nama bidang atau nomor peraturannya.";
        }

        $jenis = $documents->pluck('type')->unique()->filter()->take(3)->implode(', ');

        return "Ditemukan {$documents->count()} dokumen terkait \"{$query}\" di JDIH Kota Kendari"
            . ($jenis ? ", antara lain berupa {$jenis}" : '')
            . '. Daftar di bawah diurutkan berdasarkan kemiripan dengan pertanyaan Anda.';
    }
}
