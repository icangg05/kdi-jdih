<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Vinkla\Hashids\Facades\Hashids;

class BeritaShow extends Component
{
  public $id;

  public function mount($id)
  {
    $this->id = Hashids::decode($id)[0] ?? abort(404);
  }

  public function render()
  {
    $data = DB::table('berita')
      ->where('id', $this->id)
      ->first();
    abort_if(!$data, 404);

    $beritaTerbaru = DB::table('berita')
      ->whereNot('id', $data->id)
      ->where('status', 1)
      ->orderByDesc('tanggal')
      ->limit(3)
      ->get();

    return view('livewire.berita-show', compact(
      'data',
      'beritaTerbaru',
    ));
  }
}
