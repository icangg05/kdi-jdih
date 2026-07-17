<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InformasiHukum extends Model
{
	protected $table   = 'informasi_hukum';
	protected $guarded = [];


	protected static function boot()
	{
		parent::boot();

		static::updated(function ($model) {
			if ($model->isDirty('image'))
				Storage::delete(config('app.img_directory') . $model->getOriginal('image'));

			if ($model->isDirty('dokumen'))
				Storage::delete(config('app.doc_directory') . $model->getOriginal('dokumen'));
		});

		static::deleted(function ($model) {
			Storage::delete(config('app.img_directory') . $model->image);

			Storage::delete(config('app.doc_directory') . $model->dokumen);
		});
	}
}
