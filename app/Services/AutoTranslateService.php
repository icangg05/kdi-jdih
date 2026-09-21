<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Terjemahkan field konten dinamis (id -> en/zh/ko) via Google Translate gratis.
 *
 * ponytail: endpoint tidak resmi milik ekstensi kamus Chrome (clients5, tanpa API key).
 * translate.google.com (dipakai stichoza) sudah memblokir IP server dengan 429.
 * format=html menjaga tag & atribut (src/width gambar, kelas ql-align-*). Kegagalan
 * dibiarkan kosong (tt() fallback ke Bahasa Indonesia) dan diberitahukan ke admin,
 * TIDAK memblokir simpan. Upgrade ke Google Cloud Translate bila butuh andal.
 */
class AutoTranslateService
{
    /** @var array<int, string> */
    private array $targets = ['en', 'zh', 'ko'];

    /** @var array<string, string> */
    private array $googleMap = ['zh' => 'zh-CN'];

    /** @var array<int, string> locale yang gagal pada translate() terakhir */
    private array $failed = [];

    /**
     * @param  array<string, string>  $fields  ['judul' => '...', 'isi' => '...']
     * @return array<string, array<string, string>>  {"en":{"judul":..},"zh":{..},"ko":{..}}
     */
    public function translate(array $fields, string $source = 'id'): array
    {
        $out = [];
        $this->failed = [];

        foreach ($this->targets as $locale) {
            foreach ($fields as $key => $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    $out[$locale][$key] = '';
                    continue;
                }
                try {
                    $out[$locale][$key] = $this->google($value, $source, $locale);
                } catch (Throwable) {
                    $out[$locale][$key] = ''; // fallback ke Bahasa Indonesia lewat tt()
                    $this->failed[] = $locale;
                }
            }
        }

        return $out;
    }

    /**
     * Terjemahkan field milik $model (dari kolom asli) lalu simpan ke kolom
     * `translations`. Dipanggil controller saat checkbox "auto_translate" dicentang.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array<int, string>  $fields
     */
    public function apply($model, array $fields): void
    {
        $source = [];
        foreach ($fields as $field) {
            $source[$field] = (string) ($model->{$field} ?? '');
        }

        $model->translations = json_encode($this->translate($source), JSON_UNESCAPED_UNICODE);
        $model->save();

        if ($this->failed) {
            session()->flash('warning', 'Data tersimpan, tetapi terjemahan otomatis gagal untuk bahasa: '
                . strtoupper(implode(', ', array_unique($this->failed)))
                . '. Halaman bahasa tersebut sementara menampilkan Bahasa Indonesia — coba simpan ulang nanti.');
        }
    }

    private function google(string $text, string $source, string $target): string
    {
        $res = Http::asForm()->timeout(20)->post('https://clients5.google.com/translate_a/t?' . http_build_query([
            'client' => 'dict-chrome-ex',
            'sl'     => $this->map($source),
            'tl'     => $this->map($target),
            'format' => $text !== strip_tags($text) ? 'html' : 'text',
        ]), ['q' => $text])->throw();

        $translated = trim((string) data_get($res->json(), '0'));
        if ($translated === '') {
            throw new \RuntimeException('Respons terjemahan kosong');
        }

        return $translated;
    }

    private function map(string $locale): string
    {
        return $this->googleMap[$locale] ?? $locale;
    }

    /** Self-check tanpa jaringan: mapping locale + field kosong dilewati. */
    public static function demo(): void
    {
        $svc = new self();
        assert($svc->map('zh') === 'zh-CN');
        assert($svc->map('en') === 'en');
        $r = $svc->translate(['judul' => '', 'isi' => '   ']);
        assert($r['en']['judul'] === '' && $r['ko']['isi'] === '');
        assert(array_keys($r) === ['en', 'zh', 'ko']);
        echo "AutoTranslateService::demo OK\n";
    }
}
