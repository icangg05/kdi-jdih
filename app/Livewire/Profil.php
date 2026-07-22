<?php

namespace App\Livewire;

use Livewire\Component;

class Profil extends Component
{
  public $kategori;

  public function mount($kategori)
  {
    $this->kategori = $kategori;
  }

  public function render()
  {
    // Konten (id) + terjemahan en/zh/ko ada di lang/{locale}/profil.php
    if (! in_array($this->kategori, ['sekilas-sejarah', 'dasar-hukum', 'visi', 'misi', 'sto'], true)) {
      return abort(404);
    }

    $data = __("profil.{$this->kategori}.body");

    return view('livewire.profil', [
      'title'    => __("profil.{$this->kategori}.title"),
      'data'     => is_string($data) ? $data : null, // 'sto' body = null
      'kategori' => $this->kategori,
    ]);
  }
}
