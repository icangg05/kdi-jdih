<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
