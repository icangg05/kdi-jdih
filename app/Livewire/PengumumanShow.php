<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Vinkla\Hashids\Facades\Hashids;

class PengumumanShow extends Component
{
  public $id;

  public function mount($id)
  {
    $this->id = Hashids::decode($id)[0] ?? abort(404);
  }

  public function render()
  {
    $data = DB::table('pengumuman')
      ->where('id', $this->id)
      ->first();
    abort_if(!$data, 404);

    // Query pengumuman terbaru
    $pengumumanTerbaru = DB::table('pengumuman')
      ->where('status', 1)
      ->where('id', '!=', $this->id)
      ->orderBy('created_at', 'desc')
      ->limit(3)
      ->get();


    return view('livewire.pengumuman-show', compact(
      'data',
      'pengumumanTerbaru',
    ));
  }
}
