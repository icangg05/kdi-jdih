<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

if (! function_exists('tt')) {
  /**
   * Ambil nilai field konten dinamis sesuai locale aktif.
   * Bekerja untuk Eloquent model, stdClass (hasil DB::table), maupun array.
   * Locale default (id) = kolom asli. Locale lain = dari kolom sidecar
   * `translations` (JSON {"en":{"judul":..},...}), fallback ke asli bila kosong.
   */
  function tt($row, string $field): string
  {
    $original = (string) (data_get($row, $field) ?? '');

    // Bahasa sumber kolom asli = fallback_locale (id). Catatan: config('app.locale')
    // ikut berubah saat App::setLocale(), jadi TIDAK bisa dipakai sebagai pembanding.
    if (app()->getLocale() === config('app.fallback_locale', 'id')) {
      return $original;
    }

    $translations = data_get($row, 'translations');
    if (is_string($translations)) {
      $translations = json_decode($translations, true) ?: [];
    }
    $translated = Arr::get((array) $translations, app()->getLocale() . '.' . $field);

    return filled($translated) ? (string) $translated : $original;
  }
}

if (! function_exists('checkFilePath')) {
  function checkFilePath($directory, $file)
  {
    if (empty($file)) {
      return false;
    }

    $filePath = $directory . $file;
    if (!Storage::exists($filePath))
      return false;

    return true;
  }
}


if (! function_exists('uploadFile')) {
  function uploadFile($directory, $file)
  {
    $filename = $file->hashName();
    $file->storeAs($directory, $filename);

    return $filename;
  }
}



if (!function_exists('textLog')) {
  function textLog($controller, $username)
  {
    $now = Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY [Pukul] HH:mm:ss');

    $message = "User {$username} melakukan {$controller} pada {$now}";

    return $message;
  }
}


if (!function_exists('hitDocument')) {

  // hit_see & hit_download nullable dan banyak yang masih NULL — increment()
  // biasa (kolom = kolom + 1) menghasilkan NULL, jadi counter tidak pernah naik.
  function hitDocument($id, $column)
  {
    // $column masuk ke raw SQL — batasi ke kolom yang memang ada
    abort_unless(in_array($column, ['hit_see', 'hit_download'], true), 500);

    return \Illuminate\Support\Facades\DB::table('document')
      ->where('id', (int) $id)
      ->update([$column => \Illuminate\Support\Facades\DB::raw("COALESCE($column, 0) + 1")]);
  }
}


if (!function_exists('checkPrefixRoute')) {

  function checkPrefixRoute($tipeDokumen)
  {
    if ($tipeDokumen == 1) {
      $prefixRoute = 'peraturan';
    } elseif ($tipeDokumen == 2) {
      $prefixRoute = 'monografi';
    } elseif ($tipeDokumen == 3) {
      $prefixRoute = 'artikel';
    } else {
      $prefixRoute = 'putusan';
    }

    return $prefixRoute;
  }
}
