<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

/**
 * Terjemahkan field konten dinamis (id -> en/zh/ko) via Google Translate gratis.
 *
 * ponytail: pakai stichoza (scrape translate.google.com, tanpa API key) — rawan
 * rate-limit & ada batas panjang teks (~5000 char, teks/HTML panjang seperti `isi`
 * bisa gagal). Kegagalan dibiarkan kosong (tt() fallback ke Bahasa Indonesia),
 * TIDAK memblokir simpan. Upgrade ke Google Cloud Translate bila butuh andal.
 */
class AutoTranslateService
{
    /** @var array<int, string> */
    private array $targets = ['en', 'zh', 'ko'];

    /** @var array<string, string> */
    private array $googleMap = ['zh' => 'zh-CN'];

    /**
     * @param  array<string, string>  $fields  ['judul' => '...', 'isi' => '...']
     * @return array<string, array<string, string>>  {"en":{"judul":..},"zh":{..},"ko":{..}}
     */
    public function translate(array $fields, string $source = 'id'): array
    {
        $out = [];

        foreach ($this->targets as $locale) {
            $translator = (new GoogleTranslate())
                ->setSource($this->map($source))
                ->setTarget($this->map($locale));

            foreach ($fields as $key => $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    $out[$locale][$key] = '';
                    continue;
                }
                try {
                    $out[$locale][$key] = trim((string) $translator->translate($value));
                } catch (Throwable) {
                    $out[$locale][$key] = ''; // fallback ke Bahasa Indonesia lewat tt()
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
