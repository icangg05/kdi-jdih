<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\HasilUjiMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FormHasilUjiMateriController extends Controller
{
  // Function create hasil uji materi terkait
  public function create($idDokumen)
  {
    $title = 'Tambah Uji Materi Terkait';

    return view('backend.form-hasil-uji-materi', compact(
      'idDokumen',
      'title',
    ));
  }


  // Function store data hasil uji materi
  public function store(Request $request, $idDokumen)
  {
    $request->validate([
      'hasil_uji_materi' => ['required'],
      'status_hasum'     => ['required'],
    ]);


    $fileHasilUjiMateri = uploadFile(config('app.doc_directory'), $request->file('hasil_uji_materi'));

    HasilUjiMateri::create([
      'id_dokumen'       => (int) $idDokumen,
      'hasil_uji_materi' => $fileHasilUjiMateri,
      'status_hasum'     => trim(ucfirst($request->status_hasum)),
      'catatan_hasum'    => trim(ucfirst($request->catatan_hasum)),
      'created_at'       => Carbon::now(),
      'updated_at'       => Carbon::now(),
      '_created_by'      => Auth::user()->id,
      '_updated_by'      => Auth::user()->id,
    ]);


    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data hasil uji materi berhasil ditambahkan.',
    ]);
  }


  // Function edit hasil uji materi terkait
  public function edit($idDokumen, $idHasilUjiMateri)
  {
    $title          = 'Edit Uji Materi Terkait';
    $hasilUjiMateri = HasilUjiMateri::findOrFail($idHasilUjiMateri);

    return view('backend.form-hasil-uji-materi', compact(
      'idDokumen',
      'title',
      'hasilUjiMateri',
    ));
  }


  // Function update data hasil uji materi
  public function update(Request $request, $idDokumen, $idHasilUjiMateri)
  {
    $hasilUjiMateri = HasilUjiMateri::findOrFail($idHasilUjiMateri);

    $validationRules = [
      'status_hasum' => ['required'],
    ];

    if (!checkFilePath(config('app.doc_directory'), $hasilUjiMateri->hasil_uji_materi)) {
      $validationRules['hasil_uji_materi'] = ['required'];
    }

    // Lakukan validasi dengan aturan yang telah ditentukan
    $request->validate($validationRules);

    // Proses file jika ada
    if ($request->file('hasil_uji_materi')) {
      $fileHasilUjiMateri = uploadFile(config('app.doc_directory'), $request->file('hasil_uji_materi'));
    }

    // Update data Hasil Uji Materi
    $hasilUjiMateri->update([
      'hasil_uji_materi' => $fileHasilUjiMateri ?? $hasilUjiMateri->hasil_uji_materi,
      'status_hasum'     => trim(ucfirst($request->status_hasum)),
      'catatan_hasum'    => trim(ucfirst($request->catatan_hasum)),
      'updated_at'       => Carbon::now(),
      '_updated_by'      => Auth::user()->id,
    ]);

    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    // Redirect ke halaman dengan pesan sukses
    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data hasil uji materi berhasil diperbarui.',
    ]);
  }


  // Function delete data hasil uji materi
  public function destroy($idDokumen, $idHasilUjiMateri)
  {
    HasilUjiMateri::findOrFail($idHasilUjiMateri)->delete();

    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'info' => 'Data hasil uji materi berhasil dihapus.',
    ]);
  }
}
