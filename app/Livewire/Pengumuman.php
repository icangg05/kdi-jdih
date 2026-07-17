<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Pengumuman extends Component
{
  use WithPagination;

  #[Url()]
  public $q = '';

  public function search()
  {
    $this->resetPage();
  }

  public function render()
  {
    $data = DB::table('pengumuman')
      ->orderBy('created_at', 'desc')
      ->when(
        $this->q,
        fn($q) => $q->where('judul', 'like', "%{$this->q}%")
      )
      ->paginate(8)
      ->withQueryString();

    return view('livewire.pengumuman', compact(
      'data',
    ));
  }
}
