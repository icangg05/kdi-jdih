<?php

namespace App\Livewire\Frontend;

use App\Models\Disabilitas as DisabilitasModel;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('LAYANAN DISABILITAS - JDIH Kota Kendari')]
class Disabilitas extends Component
{
    use WithPagination;

    #[Url()]
    public $q = '', $jenis = '', $tahun = '', $nomor = '';

    // Setiap pencarian/filter berubah (live), balik ke halaman 1
    public function updated($property)
    {
        if (in_array($property, ['q', 'jenis', 'tahun', 'nomor'])) {
            $this->resetPage();
        }
    }

    public function resetFilter()
    {
        $this->reset(['q', 'jenis', 'tahun', 'nomor']);
        $this->resetPage();
    }

    public function render()
    {
        $query = DisabilitasModel::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->orderBy('created_at', 'desc');

        if ($this->q !== '') {
            $q = $this->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('abstrak', 'like', "%{$q}%")
                    ->orWhere('kata_kunci', 'like', "%{$q}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$q}%");
            });
        }
        if ($this->jenis !== '') {
            $query->where('jenis_dokumen', $this->jenis);
        }
        if ($this->tahun !== '') {
            $query->where('tahun', $this->tahun);
        }
        if ($this->nomor !== '') {
            $query->where('nomor_dokumen', 'like', "%{$this->nomor}%");
        }

        $disabilitas = $query->paginate(12)->withQueryString();

        $years = DisabilitasModel::where('status_publikasi', 'published')
            ->where('hak_akses', 'public')
            ->whereNotNull('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $hasFilter = $this->q !== '' || $this->jenis !== '' || $this->tahun !== '' || $this->nomor !== '';

        return view('livewire.frontend.disabilitas', compact('disabilitas', 'years', 'hasFilter'));
    }
}
