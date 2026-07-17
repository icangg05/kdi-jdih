<?php

namespace App\Observers;

use App\Models\Pengumuman;
use Illuminate\Support\Facades\Storage;

class PengumumanObserver
{
  protected string $docDirectory;
  protected string $imgDirectory;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
  }

  /**
   * Handle the Pengumuman "created" event.
   */
  public function created(Pengumuman $pengumuman): void
  {
    //
  }

  /**
   * Handle the Pengumuman "updated" event.
   */
  public function updated(Pengumuman $pengumuman): void
  {
    if ($pengumuman->isDirty('image'))
      Storage::delete($this->imgDirectory . $pengumuman->getOriginal('image'));

    if ($pengumuman->isDirty('dokumen'))
      Storage::delete($this->docDirectory . $pengumuman->getOriginal('dokumen'));
  }

  /** 
   * Handle the Pengumuman "deleted" event.
   */
  public function deleted(Pengumuman $pengumuman): void
  {
    Storage::delete($this->imgDirectory . $pengumuman->image);
    Storage::delete($this->docDirectory . $pengumuman->dokumen);
  }

  /**
   * Handle the Pengumuman "restored" event.
   */
  public function restored(Pengumuman $pengumuman): void
  {
    //
  }

  /**
   * Handle the Pengumuman "force deleted" event.
   */
  public function forceDeleted(Pengumuman $pengumuman): void
  {
    //
  }
}
