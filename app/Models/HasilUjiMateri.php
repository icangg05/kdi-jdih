<?php

namespace App\Models;

use App\Traits\LogsUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HasilUjiMateri extends Model
{
    use LogsUser;
    
    protected $table   = 'hasil_uji_materi';
    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();


        static::created(function ($model) {
            $model->logUser('Tambah', $model->id_dokumen);
        });

        static::updated(function ($model) {
            if ($model->isDirty('hasil_uji_materi'))
                Storage::delete(config('app.doc_directory') . $model->getOriginal('hasil_uji_materi'));

            $model->logUser('Ubah', $model->id_dokumen);
        });

        static::deleted(function ($model) {
            Storage::delete(config('app.doc_directory') . $model->hasil_uji_materi);

            $model->logUser('Hapus', $model->id_dokumen);
        });
    }
}
