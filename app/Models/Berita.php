<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Berita extends Model
{
  protected $table   = 'berita';
  protected $guarded = [];


  protected static function boot()
  {
    parent::boot();

    static::updated(function ($model) {
      if ($model->isDirty('image'))
        Storage::delete(config('app.img_directory') . $model->getOriginal('image'));
    });

    static::deleted(function ($model) {
      Storage::delete(config('app.img_directory') . $model->image);
    });
  }
}
