<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Vinkla\Hashids\Facades\Hashids;

class DokumenShow extends Component
{
  public $kategori, $id;


  public function mount($kategori, $id)
  {
    $this->kategori = $kategori;
    $this->id       = Hashids::decode($id)[0] ?? abort(404);

    hitDocument($this->id, 'hit_see');
  }


  public function render()
  {
    $eksemplar = null;
    $data      = (array) DB::table('document')
      ->where('document.id', (int) $this->id)
      ->leftJoin('data_lampiran', 'document.id', '=', 'data_lampiran.id_dokumen')
      // Singkatan bentuk peraturan diambil dari master `document_type`; kolom
      // `document.singkatan_jenis` pada data lama masih berisi nama panjang.
      ->leftJoin('document_type', fn($join) => $join
        ->on('document_type.name', '=', 'document.jenis_peraturan')
        ->where('document_type.parent_id', 1))
      ->select(
        'document.*',
        'data_lampiran.judul_lampiran',
        'data_lampiran.dokumen_lampiran',
        'document_type.singkatan as singkatan_master',
      )
      ->first();

    if ($this->kategori === 'peraturan') {
      $title = 'Peraturan dan Keputusan';
    } elseif ($this->kategori === 'monografi') {
      $title     = 'Monografi Hukum';
      $eksemplar = DB::table('eksemplar')->where('id_dokumen', (int) $data['id'])->get();
    } elseif ($this->kategori === 'artikel') {
      $title = 'Artikel / Majalah Hukum';
    } elseif ($this->kategori === 'putusan') {
      $title = 'Putusan';
    } else {
      return abort(404);
    }

    abort_if(!$data, 404);

    $subjek = DB::table('data_subyek')->where('id_dokumen', (int) $data['id'])->get();
    $dataPengarang = DB::table('data_pengarang')
      ->where('data_pengarang.id_dokumen', (int) $data['id'])
      ->join('pengarang', 'data_pengarang.nama_pengarang', 'pengarang.id')
      ->join('tipe_pengarang', 'data_pengarang.tipe_pengarang', '=', 'tipe_pengarang.id')
      ->join('jenis_pengarang', 'data_pengarang.jenis_pengarang', '=', 'jenis_pengarang.id')
      ->select('data_pengarang.*', 'pengarang.name as nama_pengarang', 'tipe_pengarang.name as tipe_pengarang', 'jenis_pengarang.name as jenis_pengarang')
      ->get();
    // Status peraturan ditulis lengkap seperti lembar kerja pusat:
    // "Mencabut <judul peraturan yang dicabut>". Satu dokumen bisa punya
    // beberapa baris status, jadi diambil semuanya.
    $dataStatus = DB::table('data_status')
      ->where('data_status.id_dokumen', (int) $data['id'])
      ->leftJoin('document as target', 'target.id', '=', 'data_status.id_dokumen_target')
      ->orderBy('data_status.urutan')
      ->select(
        'data_status.status_peraturan',
        'data_status.catatan_status_peraturan',
        'data_status.id_dokumen_target',
        'target.judul as judul_target',
        'target.tipe_dokumen as tipe_target',
      )
      ->get();

    $peraturanTerkait = DB::table('peraturan_terkait')
      ->where('id_dokumen', (int) $data['id'])
      ->leftJoin('document', 'peraturan_terkait.peraturan_terkait', '=', 'document.id')
      ->select('peraturan_terkait.*', 'document.judul as judul_peraturan_terkait')
      ->get();

    $hasilUjiMateri = DB::table('hasil_uji_materi')
      ->where('id_dokumen', (int) $data['id'])
      ->get();

    // Peraturan turunan yang diterbitkan untuk melaksanakan peraturan ini.
    $peraturanPelaksana = DB::table('peraturan_pelaksana')
      ->where('id_dokumen', (int) $data['id'])
      ->leftJoin('document', 'peraturan_pelaksana.peraturan_pelaksana', '=', 'document.id')
      ->orderBy('peraturan_pelaksana.urutan')
      ->select('peraturan_pelaksana.*', 'document.judul as judul_peraturan_pelaksana')
      ->get();

    $kategori = $this->kategori;

    return view('livewire.dokumen-show', compact(
      'title',
      'kategori',
      'data',
      'subjek',
      'dataPengarang',
      'dataStatus',
      'eksemplar',
      'peraturanTerkait',
      'hasilUjiMateri',
      'peraturanPelaksana',
    ));
  }
}
