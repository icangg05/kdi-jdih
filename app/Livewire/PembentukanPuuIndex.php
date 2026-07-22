<?php

namespace App\Livewire;

use App\Models\PembentukanPuu;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PembentukanPuuIndex extends Component
{
  use WithPagination;

  #[Url()]
  public $kategori = 'naskah-akademik';

  #[Url()]
  public $q = '';

  #[Url()]
  public $tahun = '';

  #[Url()]
  public $nomor = '';

  // value (frontend) => [label, kolom db (jenis_dokumen), ikon]
  public array $categories = [
    ['value' => 'naskah-akademik',              'label' => 'Naskah Akademik',                       'db' => 'naskah_akademik',              'icon' => 'fa-book'],
    ['value' => 'naskah-keterangan-penjelasan', 'label' => 'Naskah Keterangan/Penjelasan',          'db' => 'naskah_keterangan_penjelasan', 'icon' => 'fa-file-lines'],
    ['value' => 'rancangan-puu',                'label' => 'Rancangan PUU',                         'db' => 'rancangan_puu',                'icon' => 'fa-file-pen'],
    ['value' => 'penelitian-hukum',             'label' => 'Penelitian Hukum',                      'db' => 'penelitian_hukum',             'icon' => 'fa-magnifying-glass'],
    ['value' => 'pengkajian-hukum',             'label' => 'Pengkajian Hukum',                      'db' => 'pengkajian_hukum',             'icon' => 'fa-scale-balanced'],
    ['value' => 'pengkajian-konstitusi',        'label' => 'Pengkajian Konstitusi',                 'db' => 'pengkajian_konstitusi',        'icon' => 'fa-landmark'],
    ['value' => 'analisis-evaluasi',            'label' => 'Analisis & Evaluasi',                   'db' => 'analisis_evaluasi',            'icon' => 'fa-chart-line'],
  ];

  public function mount($kategori = null)
  {
    if ($kategori) {
      $this->kategori = $kategori;
    }
    if (! $this->currentCategory()) {
      abort(404);
    }
  }

  public function selectKategori($value)
  {
    $this->kategori = $value;
    $this->resetPage();
  }

  // Live: setiap filter/pencarian/kategori berubah, kembali ke halaman 1
  public function updated($property)
  {
    if (in_array($property, ['q', 'tahun', 'nomor', 'kategori'])) {
      $this->resetPage();
    }
  }

  public function resetFilter()
  {
    $this->reset(['q', 'tahun', 'nomor']);
    $this->resetPage();
  }

  private function currentCategory(): ?array
  {
    return collect($this->categories)->firstWhere('value', $this->kategori);
  }

  public function render()
  {
    $current = $this->currentCategory();
    $dbValue = $current['db'] ?? null;

    $counts = PembentukanPuu::query()
      ->where('status_publikasi', 'published')
      ->where('hak_akses', 'public')
      ->selectRaw('jenis_dokumen, count(*) as c')
      ->groupBy('jenis_dokumen')
      ->pluck('c', 'jenis_dokumen');

    $data = PembentukanPuu::query()
      ->where('jenis_dokumen', $dbValue)
      ->where('status_publikasi', 'published')
      ->where('hak_akses', 'public')
      ->when($this->q, function ($query) {
        $s = '%' . $this->q . '%';
        $query->where(function ($w) use ($s) {
          $w->where('judul', 'like', $s)
            ->orWhere('abstrak', 'like', $s)
            ->orWhere('kata_kunci', 'like', $s)
            ->orWhere('penulis', 'like', $s)
            ->orWhere('lembaga_pemrakarsa', 'like', $s)
            ->orWhere('nomor_dokumen', 'like', $s);
        });
      })
      ->when($this->tahun, fn($query) => $query->where('tahun', $this->tahun))
      ->when($this->nomor, fn($query) => $query->where('nomor_dokumen', 'like', '%' . $this->nomor . '%'))
      ->orderByDesc('created_at')
      ->paginate(9);

    return view('livewire.pembentukan-puu', [
      'data'    => $data,
      'counts'  => $counts,
      'current' => $current,
    ]);
  }
}
