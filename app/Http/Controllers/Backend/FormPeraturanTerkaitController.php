<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\PeraturanTerkait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FormPeraturanTerkaitController extends Controller
{
  // Function create data peraturan terkait
  public function create($idDokumen)
  {
    $title = 'Tambah Peraturan Terkait';
    $dataStatus = [
      ['label' => 'Melaksanakan', 'value' => 'Melaksanakan'],
      ['label' => 'Dilaksanakan', 'value' => 'Dilaksanakan'],
    ];
    $dataPeraturan = Document::where('tipe_dokumen', 1)->pluck('judul', 'id')
      ->map(fn($judul, $id) => ['label' => $judul, 'value' => $id]);


    return view('backend.form-peraturan-terkait', compact(
      'idDokumen',
      'title',
      'dataStatus',
      'dataPeraturan',
    ));
  }


  // Function edit data peraturan terkait
  public function edit($idDokumen, $id)
  {
    $peraturanTerkait = PeraturanTerkait::findOrFail($id);

    $title = 'Edit Peraturan Terkait';
    $dataStatus = [
      ['label' => 'Melaksanakan', 'value' => 'Melaksanakan'],
      ['label' => 'Dilaksanakan', 'value' => 'Dilaksanakan'],
    ];
    $dataPeraturan = Document::where('tipe_dokumen', 1)->pluck('judul', 'id')
      ->map(fn($judul, $id) => ['label' => $judul, 'value' => $id]);


    return view('backend.form-peraturan-terkait', compact(
      'idDokumen',
      'title',
      'dataStatus',
      'dataPeraturan',
      'peraturanTerkait'
    ));
  }


  // Function store data peraturan terkait
  public function store(Request $request, $idDokumen)
  {
    $request->validate([
      'status_perter'     => ['required'],
      'peraturan_terkait' => ['required'],
    ]);

    PeraturanTerkait::create([
      'id_dokumen'        => (int) $idDokumen,
      'peraturan_terkait' => $request->peraturan_terkait,
      'status_perter'     => $request->status_perter,
      'catatan_perter'    => trim(ucfirst($request->catatan_perter)),
      'created_at'        => Carbon::now(),
      'updated_at'        => Carbon::now(),
      '_created_by'       => Auth::user()->id,
      '_updated_by'       => Auth::user()->id,
    ]);


    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data peraturan terkait berhasil ditambahkan.',
    ]);
  }


  // Function update data peraturan terkait
  public function update(Request $request, $idDokumen, $idPeraturanTerkait)
  {
    $request->validate([
      'status_perter'     => ['required'],
      'peraturan_terkait' => ['required'],
    ]);

    PeraturanTerkait::findOrFail($idPeraturanTerkait)->update([
      'id_dokumen'        => (int) $idDokumen,
      'peraturan_terkait' => (int) $request->peraturan_terkait,
      'status_perter'     => $request->status_perter,
      'catatan_perter'    => trim(ucfirst($request->catatan_perter)),
      'updated_at'        => Carbon::now(),
      '_updated_by'       => Auth::user()->id,
    ]);


    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);


    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'success' => 'Data peraturan terkait berhasil diupdate.',
    ]);
  }


  // Function delete data peraturan terkait
  public function destroy($idDokumen, $idPeraturanTerkait)
  {
    PeraturanTerkait::findOrFail($idPeraturanTerkait)->delete();

    $tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
    $prefixRoute = checkPrefixRoute($tipeDokumen);

    return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
      'info' => 'Data peraturan terkait berhasil dihapus.',
    ]);
  }
}
