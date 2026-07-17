<?php

namespace App\Observers;

use App\Models\DataLampiran;
use Illuminate\Support\Facades\Storage;

class DataLampiranObserver
{
  protected string $docDirectory;
  protected string $imgDirectory;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
  }


  /**
   * Handle the DataLampiran "created" event.
   */
  public function created(DataLampiran $dataLampiran): void
  {
    //
  }

  /**
   * Handle the DataLampiran "updating" event.
   */
  public function updating(DataLampiran $dataLampiran): void
  {
    if ($dataLampiran->isDirty('dokumen_lampiran')) {
      Storage::delete($this->docDirectory . $dataLampiran->getOriginal('dokumen_lampiran'));
    }
  }

  /**
   * Handle the DataLampiran "deleted" event.
   */
  public function deleted(DataLampiran $dataLampiran): void
  {
    //
  }

  /**
   * Handle the DataLampiran "restored" event.
   */
  public function restored(DataLampiran $dataLampiran): void
  {
    //
  }

  /**
   * Handle the DataLampiran "force deleted" event.
   */
  public function forceDeleted(DataLampiran $dataLampiran): void
  {
    //
  }
}
