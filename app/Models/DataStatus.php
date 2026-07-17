<?php

namespace App\Models;

use App\Traits\LogsUser;
use Illuminate\Database\Eloquent\Model;

class DataStatus extends Model
{
    use LogsUser;

    protected $table   = 'data_status';
    protected $guarded = [];

    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->logUser('Tambah', $model->id_dokumen);
        });

        static::updated(function ($model) {
            $model->logUser('Ubah', $model->id_dokumen);
        });

        static::deleted(function ($model) {
            $model->logUser('Hapus', $model->id_dokumen);
        });
    }
}
