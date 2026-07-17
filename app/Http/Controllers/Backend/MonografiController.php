<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DataLampiran;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonografiController extends Controller
{
  protected string $docDirectory;
  protected string $imgDirectory;
  protected array $validate;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
    $this->validate = [
      'jenis_peraturan'  => ['required'],
      'judul'            => ['required'],
      'tahun_terbit'     => ['required', 'numeric'],
      'penerbit'         => ['required'],
      'tempat_terbit'    => ['required'],
      'sumber_perolehan' => ['required'],
    ];
  }


  public function index(Request $request)
  {
    $title = 'Monografi Hukum';
    $data  = DB::table('document')
      ->where('tipe_dokumen', 2)
      ->leftJoin('data_subyek', 'document.id', '=', 'data_subyek.id_dokumen')
      ->leftJoin('eksemplar', 'document.id', '=', 'eksemplar.id_dokumen')
      ->select(
        'document.id',
        'judul',
        'jenis_peraturan',
        'tahun_terbit',
        'document.sumber_perolehan',
        'data_subyek.subyek',
        'eksemplar.kode_eksemplar'
      );

    if ($request->filled('jenis_peraturan'))
      $data->where('jenis_peraturan', $request->jenis_peraturan);

    if ($request->filled('judul'))
      $data->where('judul', 'like', "%$request->judul%");

    if ($request->filled('tahun_terbit'))
      $data->where('tahun_terbit', 'like', "%$request->tahun_terbit%");

    if ($request->filled('sumber_perolehan'))
      $data->where('document.sumber_perolehan', 'like', '%' . $request->sumber_perolehan . '%');

    if ($request->filled('subyek'))
      $data->where('subyek', 'like', '%' . $request->subyek . '%');

    if ($request->filled('kode_eksemplar'))
      $data->where('kode_eksemplar', 'like', '%' . $request->kode_eksemplar . '%');

    $data = $data->orderBy('document.created_at', 'desc')->paginate(15)->withQueryString();


    return view('backend.monografi', compact(
      'title',
      'data',
    ));
  }


  public function show($id)
  {
    $tipeDokumen = 'monografi';
    $title       = 'Detail Monografi';

    $data        = DB::table('document')
      ->where('document.id', $id)
      ->leftJoin('data_lampiran', 'document.id', '=', 'data_lampiran.id_dokumen')
      ->leftJoin('user as creator', 'document._created_by', '=', 'creator.id')
      ->leftJoin('user as updater', 'document._created_by', '=', 'updater.id')
      ->select('document.*', 'creator.username as _created_by', 'updater.username as _updated_by', 'data_lampiran.judul_lampiran', 'data_lampiran.deskripsi_lampiran', 'data_lampiran.dokumen_lampiran')
      ->first();


    return view('backend.monografi-view', compact(
      'tipeDokumen',
      'title',
      'data',
    ));
  }


  public function create()
  {
    $title = 'Tambah Data Monografi';

    return view('backend.monografi-form', compact(
      'title',
    ));
  }


  public function store(Request $request)
  {
    $request->validate($this->validate);

    // Upload abstrak
    if ($request->file('abstrak'))
      $abstrak = uploadFile($this->docDirectory, $request->file('abstrak'));
    // Upload sampul
    if ($request->file('gambar_sampul'))
      $gambarSampul = uploadFile($this->imgDirectory, $request->file('gambar_sampul'));

    // Set data document
    $data = $request->except('_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');

    $data['tipe_dokumen']  = 2;
    $data['abstrak']       = $abstrak ?? null;
    $data['gambar_sampul'] = $gambarSampul ?? null;
    $data['created_at']    = Carbon::now();
    $data['_created_by']   = Auth::user()->id;
    $data['updated_at']    = Carbon::now();
    $data['_updated_by']   = Auth::user()->id;

    // Store to database
    $dataId = Document::create($data)->id;


    // Process lampiran data
    if ($request->judul_lampiran && $request->dokumen_lampiran) {
      // Store and upload dokumen lampiran
      $dokumenLampiran = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));

      // Set data lampiran
      $lampiran['id_dokumen']         = $dataId;
      $lampiran['judul_lampiran']     = $request->judul_lampiran;
      $lampiran['deskripsi_lampiran'] = $request->deskripsi_lampiran;
      $lampiran['dokumen_lampiran']   = $dokumenLampiran ?? null;
      $lampiran['created_at']         = Carbon::now();
      $lampiran['_created_by']        = Auth::user()->id;
      $lampiran['updated_at']         = Carbon::now();
      $lampiran['_updated_by']        = Auth::user()->id;

      // Store to database
      DataLampiran::create($lampiran);
    }


    return redirect()->route('backend.monografi.show', $dataId)->with('success', 'Data monografi berhasil ditambahkan.');
  }


  public function edit($id)
  {
    $title = 'Edit Data Monografi';
    $data = Document::findOrFail($id);
    $lampiran  = DB::table('data_lampiran')->where('id_dokumen', $data->id)->first();

    return view('backend.monografi-form', compact(
      'title',
      'data',
      'lampiran',
    ));
  }


  public function update(Request $request, $id)
  {
    $request->validate($this->validate);

    $dataUpdate = Document::findOrFail($id);

    // Upload abstrak
    if ($request->file('abstrak'))
      $abstrak = uploadFile($this->docDirectory, $request->file('abstrak'));
    // Upload sampul
    if ($request->file('gambar_sampul'))
      $gambarSampul = uploadFile($this->imgDirectory, $request->file('gambar_sampul'));

    // Set data document
    $data = $request->except('_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');

    $data['abstrak']       = $abstrak ?? $dataUpdate->abstrak;
    $data['gambar_sampul'] = $gambarSampul ?? $dataUpdate->gambar_sampul;
    $data['updated_at']    = Carbon::now();
    $data['_updated_by']   = Auth::user()->id;

    $dataUpdate->update($data);


    // Process lampiran data
    if ($request->judul_lampiran) {
      $lampiranUpdate = DataLampiran::where('id_dokumen', $id)->first();

      // Set data lampiran
      $lampiran['judul_lampiran']     = $request->judul_lampiran;
      $lampiran['deskripsi_lampiran'] = $request->deskripsi_lampiran;
      $lampiran['updated_at']         = Carbon::now();

      if (!$lampiranUpdate) {
        $lampiran['dokumen_lampiran'] = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));
        $lampiran['id_dokumen']       = $id;
        $lampiran['created_at']       = Carbon::now();

        DataLampiran::create($lampiran);
        // 
      } else {
        if ($request->file('dokumen_lampiran'))
          $dokumenLampiran = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));

        $lampiran['dokumen_lampiran'] = $dokumenLampiran ?? $lampiranUpdate->dokumen_lampiran;
        $lampiranUpdate->update($lampiran);
      }
    }


    return redirect()->route('backend.monografi.show', $id)->with('success', 'Data monografi berhasil diupdate.');
  }


  public function destroy($id)
  {
    Document::findOrFail((int) $id)->delete();
    return redirect()->route('backend.monografi.index')->with('info', 'Data monografi berhasil dihapus.');
  }
}
