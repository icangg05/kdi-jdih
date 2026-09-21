<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\PeraturanPelaksana;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FormPeraturanPelaksanaController extends Controller
{
  // Daftar peraturan untuk dropdown; dokumen ini sendiri tidak bisa jadi pelaksananya
  private function dataPeraturan($idDokumen)
  {
    return Document::where('tipe_dokumen', 1)
      ->where('id', '!=', (int) $idDokumen)
      ->pluck('judul', 'id')
      ->map(fn($judul, $id) => ['label' => $judul, 'value' => $id]);
  }


  // Validasi + susun data sesuai sumber: pilih peraturan yang ada, atau unggah dokumen baru.
  // Kolom milik sumber lain dikosongkan supaya satu baris tidak punya dua isi sekaligus.
  private function payload(Request $request, ?PeraturanPelaksana $lama = null)
  {
    $request->validate([
      'sumber' => ['required', Rule::in(['pilih', 'upload'])],
    ]);

    if ($request->sumber === 'pilih') {
      $request->validate([
        'peraturan_pelaksana' => ['required', 'integer', Rule::exists('document', 'id')->where('tipe_dokumen', 1)],
      ]);

      return [
        'peraturan_pelaksana' => (int) $request->peraturan_pelaksana,
        'judul_pelaksana'     => null,
        'file_pelaksana'      => null,
      ];
    }

    // Berkas wajib kecuali baris lama sudah punya berkas yang masih ada di storage
    $punyaBerkas = $lama && checkFilePath(config('app.doc_directory'), $lama->file_pelaksana);

    $request->validate([
      'judul_pelaksana' => ['required', 'string', 'max:255'],
      'file_pelaksana'  => [$punyaBerkas ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:20480'],
    ]);

    return [
      'peraturan_pelaksana' => null,
      'judul_pelaksana'     => trim($request->judul_pelaksana),
      'file_pelaksana'      => $request->hasFile('file_pelaksana')
        ? uploadFile(config('app.doc_directory'), $request->file('file_pelaksana'))
        : $lama->file_pelaksana,
    ];
  }


  // Function create data peraturan pelaksana
  public function create($idDokumen)
  {
    $title         = 'Tambah Peraturan Pelaksana';
    $dataPeraturan = $this->dataPeraturan($idDokumen);


    return view('backend.form-peraturan-pelaksana', compact(
      'idDokumen',
      'title',
      'dataPeraturan',
    ));
  }


  // Function edit data peraturan pelaksana
  public function edit($idDokumen, $id)
  {
    $peraturanPelaksana = PeraturanPelaksana::findOrFail($id);

    $title         = 'Edit Peraturan Pelaksana';
    $dataPeraturan = $this->dataPeraturan($idDokumen);


    return view('backend.form-peraturan-pelaksana', compact(
      'idDokumen',
      'title',
      'dataPeraturan',
      'peraturanPelaksana',
    ));
  }


  // Function store data peraturan pelaksana
  public function store(Request $request, $idDokumen)
  {
    PeraturanPelaksana::create([
      ...$this->payload($request),
      'id_dokumen'        => (int) $idDokumen,
      'catatan_pelaksana' => trim(ucfirst($request->catatan_pelaksana)),
      'created_at'        => Carbon::now(),
      'updated_at'        => Carbon::now(),
      '_created_by'       => Auth::user()->id,
      '_updated_by'       => Auth::user()->id,
    ]);


    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data peraturan pelaksana berhasil ditambahkan.',
    ]);
  }


  // Function update data peraturan pelaksana
  public function update(Request $request, $idDokumen, $idPeraturanPelaksana)
  {
    $peraturanPelaksana = PeraturanPelaksana::findOrFail($idPeraturanPelaksana);

    // Hook `updated` di model membuang berkas lama bila berkasnya berganti/dikosongkan
    $peraturanPelaksana->update([
      ...$this->payload($request, $peraturanPelaksana),
      'id_dokumen'        => (int) $idDokumen,
      'catatan_pelaksana' => trim(ucfirst($request->catatan_pelaksana)),
      'updated_at'        => Carbon::now(),
      '_updated_by'       => Auth::user()->id,
    ]);


    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data peraturan pelaksana berhasil diupdate.',
    ]);
  }


  // Function delete data peraturan pelaksana (berkas unggahan ikut dihapus lewat hook model)
  public function destroy($idDokumen, $idPeraturanPelaksana)
  {
    PeraturanPelaksana::findOrFail($idPeraturanPelaksana)->delete();

    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);

    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'info' => 'Data peraturan pelaksana berhasil dihapus.',
    ]);
  }
}
