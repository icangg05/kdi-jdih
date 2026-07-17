<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DataLampiran;
use App\Models\Document;
use App\Models\HasilUjiMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PeraturanController extends Controller
{
  protected string $docDirectory;
  protected string $imgDirectory;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
  }


  public function index(Request $request)
  {
    $data = DB::table('document')
      ->where('tipe_dokumen', 1)
      ->leftJoin('data_status', 'document.id', '=', 'data_status.id_dokumen')
      ->select(
        'document.id',
        'bentuk_peraturan',
        'document.nomor_peraturan',
        'tahun_terbit',
        'judul',
        'document.status as status',
        'data_status.status_peraturan as status_peraturan',
        'document.created_at',
      );

    if ($request->filled('bentuk_peraturan'))
      $data->where('bentuk_peraturan', $request->bentuk_peraturan);

    if ($request->filled('status'))
      $data->where('document.status', $request->status);

    if ($request->filled('status_peraturan'))
      $data->where('data_status.status_peraturan', $request->status_peraturan);

    if ($request->filled('nomor_peraturan'))
      $data->where('nomor_peraturan', 'like', '%' . $request->nomor_peraturan . '%');

    if ($request->filled('tahun_terbit'))
      $data->where('tahun_terbit', 'like', '%' . $request->tahun_terbit . '%');

    if ($request->filled('judul'))
      $data->where('judul', 'like', '%' . $request->judul . '%');

    $data = $data->orderBy('created_at', 'desc')->paginate(15)->withQueryString();


    return view('backend.peraturan', compact(
      'data',
    ));
  }


  public function show($id)
  {
    $tipeDokumen = 'peraturan';
    $title     = 'Detail Peraturan';
    $peraturan = DB::table('document')
      ->where('document.id', $id)
      ->leftJoin('data_lampiran', 'document.id', '=', 'data_lampiran.id_dokumen')
      ->leftJoin('user as creator', 'document._created_by', '=', 'creator.id')
      ->leftJoin('user as updater', 'document._created_by', '=', 'updater.id')
      ->select('document.*', 'creator.username as _created_by', 'updater.username as _updated_by', 'data_lampiran.judul_lampiran', 'data_lampiran.deskripsi_lampiran', 'data_lampiran.dokumen_lampiran')
      ->first();

    // Query data peraturan terkait
    $dataPeraturanTerkait = DB::table('peraturan_terkait')
      ->where('id_dokumen', (int) $id)
      ->leftJoin('document', 'peraturan_terkait.peraturan_terkait', '=', 'document.id')
      ->select('peraturan_terkait.*', 'document.judul as judul_peraturan_terkait')
      ->get();

    // Query data hasil uji materi
    $dataHasilUjiMateri = HasilUjiMateri::where('id_dokumen', (int) $id)->get();

    // Query data status
    $dataStatus = DB::table('data_status')
      ->where('id_dokumen', (int) $peraturan->id)
      ->leftJoin('document', 'data_status.id_dokumen_target', '=', 'document.id')
      ->select('data_status.*', 'document.judul as judul_peraturan')
      ->get();


    return view('backend.peraturan-view', compact(
      'tipeDokumen',
      'title',
      'peraturan',
      'dataPeraturanTerkait',
      'dataHasilUjiMateri',
      'dataStatus',
    ));
  }


  public function create()
  {
    $dataTipeDokumen   = DB::table('document_type')->get();
    $selectTipeDokumen = DB::table('document_type')
      ->where('parent_id', 1)
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->id]);

    $selectTempatPenetapan = Storage::exists('data_wilayah.json')
      ? json_decode(Storage::get('data_wilayah.json'), true) : [];

    $selectBahasa = DB::table('bahasa')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();

    $selectUrusanPemerintahan = DB::table('urusan_pemerintahan')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();

    $selectBidangHukum = DB::table('bidang_hukum')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();

    return view('backend.peraturan-form', compact(
      'dataTipeDokumen',
      'selectTipeDokumen',
      'selectTempatPenetapan',
      'selectBahasa',
      'selectUrusanPemerintahan',
      'selectBidangHukum',
    ));
  }



  public function store(Request $request)
  {
    $request->validate([
      'jenis_peraturan'      => ['required'],
      'bentuk_peraturan'     => ['required'],
      'judul'                => ['required'],
      'nomor_peraturan'      => ['required'],
      'tahun_terbit'         => ['required', 'numeric'],
      'tanggal_penetapan'    => ['required', 'date'],
      'tanggal_pengundangan' => ['nullable', 'date'],
    ]);

    $data           = $request->except('_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');
    $jenisPeraturan = DB::table('document_type')->where('id', (int) $request->jenis_peraturan)->first();

    // Upload abstrak
    if ($request->file('abstrak'))
      $abstrak = uploadFile($this->docDirectory, $request->file('abstrak'));

    // Set data document
    $data['tipe_dokumen']         = 1;
    $data['jenis_peraturan']      = $jenisPeraturan->name;
    $data['singkatan_jenis']      = $jenisPeraturan->singkatan;
    $data['tanggal_penetapan']    = Carbon::createFromFormat('d-F-Y', $data['tanggal_penetapan'])->format('Y-m-d');
    $data['tanggal_pengundangan'] = $data['tanggal_pengundangan']
      ? Carbon::createFromFormat('d-F-Y', $data['tanggal_pengundangan'])->format('Y-m-d') : null;
    $data['abstrak']     = $abstrak ?? null;
    $data['created_at']  = now();
    $data['updated_at']  = now();

    // Store to database
    $peraturanId = Document::create($data)->id;


    // Process lampiran data
    if ($request->judul_lampiran && $request->dokumen_lampiran) {
      // Store and upload dokumen lampiran
      if ($request->file('dokumen_lampiran'))
        $dokumenLampiran = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));

      // Set data lampiran
      $lampiran['id_dokumen']         = $peraturanId;
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


    return redirect()->route('backend.peraturan.index')->with('success', 'Data peraturan berhasil ditambahkan.');
  }



  public function edit($id)
  {
    $peraturan = Document::findOrFail($id);
    $lampiran  = DB::table('data_lampiran')->where('id_dokumen', $peraturan->id)->first();

    $dataTipeDokumen   = DB::table('document_type')->get();
    $selectTipeDokumen = DB::table('document_type')
      ->where('parent_id', 1)
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->id]);

    $selectTempatPenetapan = Storage::exists('data_wilayah.json')
      ? json_decode(Storage::get('data_wilayah.json'), true) : [];

    $selectBahasa = DB::table('bahasa')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();

    $selectUrusanPemerintahan = DB::table('urusan_pemerintahan')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();

    $selectBidangHukum = DB::table('bidang_hukum')
      ->get()
      ->map(fn($item) => ['label' => $item->name, 'value' => $item->name])->toArray();


    return view('backend.peraturan-form', compact(
      'peraturan',
      'lampiran',
      'dataTipeDokumen',
      'selectTipeDokumen',
      'selectTempatPenetapan',
      'selectBahasa',
      'selectUrusanPemerintahan',
      'selectBidangHukum',
    ));
  }



  public function update(Request $request, $id)
  {
    $request->validate([
      'jenis_peraturan'      => ['required'],
      'bentuk_peraturan'     => ['required'],
      'judul'                => ['required'],
      'nomor_peraturan'      => ['required'],
      'tahun_terbit'         => ['required', 'numeric'],
      'tanggal_penetapan'    => ['required', 'date'],
      'tanggal_pengundangan' => ['nullable', 'date'],
    ]);

    // Get data peraturan
    $peraturan      = Document::findOrFail($id);
    // Data request convert and except another data
    $data           = $request->except('_method', '_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');
    $jenisPeraturan = DB::table('document_type')->where('id', (int) $request->jenis_peraturan)->first();

    // Upload abstrak
    if ($request->file('abstrak'))
      $abstrak = uploadFile($this->docDirectory, $request->file('abstrak'));

    // Set data document
    $data['jenis_peraturan']      = $jenisPeraturan->name;
    $data['singkatan_jenis']      = $jenisPeraturan->singkatan;
    $data['tanggal_penetapan']    = Carbon::createFromFormat('d-F-Y', $data['tanggal_penetapan'])->format('Y-m-d');
    $data['tanggal_pengundangan'] = $data['tanggal_pengundangan']
      ? Carbon::createFromFormat('d-F-Y', $data['tanggal_pengundangan'])->format('Y-m-d') : null;
    $data['abstrak']     = $abstrak ?? $peraturan->abstrak;
    $data['updated_at']  = Carbon::now();
    $data['_updated_by'] = Auth::user()->id;

    // Update data peraturan
    $peraturan->update($data);


    if ($request->judul_lampiran) {
      // Get data lmapiran
      $lampiranUpdate = DataLampiran::where('id_dokumen', $peraturan->id)->first();

      // Update and upload dokumen lampiran
      if ($request->file('dokumen_lampiran'))
        $dokumenLampiran = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));

      // Set data lampiran
      $lampiran['judul_lampiran']     = $request->judul_lampiran;
      $lampiran['deskripsi_lampiran'] = $request->deskripsi_lampiran;
      $lampiran['dokumen_lampiran']   = $dokumenLampiran ?? $lampiranUpdate->dokumen_lampiran;
      $lampiran['updated_at']         = Carbon::now();
      $lampiran['_updated_by']        = Auth::user()->id;

      if (!$lampiranUpdate) {
        // Create if not lampiran
        $lampiran['id_dokumen'] = $peraturan->id;
        DataLampiran::create($lampiran);
      } else {
        // Update to database
        $lampiranUpdate->update($lampiran);
      }
    }


    return redirect()->route('backend.peraturan.index')->with('success', 'Data peraturan berhasil diupdate.');
  }


  public function destroy($id)
  {
    Document::findOrFail((int) $id)->delete();
    return redirect()->route('backend.peraturan.index')->with('info', 'Data peraturan berhasil dihapus.');
  }
}
