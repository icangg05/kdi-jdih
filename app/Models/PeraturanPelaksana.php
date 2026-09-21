<?php

namespace App\Models;

use App\Traits\LogsUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PeraturanPelaksana extends Model
{
  use LogsUser;

  protected $table   = 'peraturan_pelaksana';
  protected $guarded = [];


  protected static function boot()
  {
    parent::boot();

    static::created(function ($model) {
      $model->logUser('Tambah', $model->id_dokumen);
    });

    static::updated(function ($model) {
      // Berkas lama dibuang saat diganti, atau saat sumber diubah ke "pilih peraturan"
      if ($model->wasChanged('file_pelaksana') && filled($model->getOriginal('file_pelaksana')))
        Storage::delete(config('app.doc_directory') . $model->getOriginal('file_pelaksana'));

      $model->logUser('Ubah', $model->id_dokumen);
    });

    static::deleted(function ($model) {
      if (filled($model->file_pelaksana))
        Storage::delete(config('app.doc_directory') . $model->file_pelaksana);

      $model->logUser('Hapus', $model->id_dokumen);
    });
  }
}
