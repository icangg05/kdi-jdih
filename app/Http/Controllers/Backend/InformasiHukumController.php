<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InformasiHukum;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InformasiHukumController extends Controller
{
  protected string $docDirectory;
  protected string $imgDirectory;
  protected array $validate;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
    $this->validate = [
      'tanggal' => ['required', 'date'],
      'judul'   => ['required'],
      'isi'     => ['required'],
      'image'   => ['nullable', 'image', 'max:10240'],
      'dokumen' => ['nullable', 'max:10240'],
      'status'  => ['required'],
    ];
  }


  public function index(Request $request)
  {
    // Set session MySQL agar nama bulan/hari pakai Bahasa Indonesia
    DB::statement("SET lc_time_names = 'id_ID'");

    $data = DB::table('informasi_hukum');

    if ($request->filled('judul'))
      $data->where('judul', 'like', '%' . $request->judul . '%');

    if ($request->filled('tanggal')) {
      $data->whereRaw("DATE_FORMAT(created_at, '%d %M %Y') LIKE ?", ['%' . $request->tanggal . '%']);
    }

    if ($request->filled('isi')) {
      $data->where('isi', 'like', '%' . $request->isi . '%');
    }

    $data = $data->orderBy('created_at', 'desc')->paginate(15)->withQueryString();


    return view('backend.informasi-hukum', compact(
      'data'
    ));
  }



  public function show($id)
  {
    $title = 'Detail Informasi Hukum';
    $data  = DB::table('informasi_hukum')
      ->where('informasi_hukum.id', $id)
      ->leftJoin('user as creator', 'informasi_hukum.created_by', '=', 'creator.id')
      ->leftJoin('user as updater', 'informasi_hukum.updated_by', '=', 'updater.id')
      ->select('informasi_hukum.*', 'creator.username as created_by', 'updater.username as updated_by')
      ->first();

    return view('backend.informasi-hukum-view', compact(
      'title',
      'data',
    ));
  }



  public function create()
  {
    $title = 'Tambah Data Informasi Hukum';
    return view('backend.informasi-hukum-form', compact(
      'title',
    ));
  }



  public function store(Request $request)
  {
    // Validate data
    $request->validate($this->validate);

    // Upload image and dokumenif exists
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    if ($request->file('dokumen'))
      $dokumen = uploadFile($this->docDirectory, $request->file('dokumen'));

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['image']      = $image ?? '';
    $data['dokumen']    = $dokumen ?? '';
    $data['created_at'] = now();
    $data['created_by'] = Auth::user()->id;
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    $dataId = InformasiHukum::create($data);


    return redirect()->route('backend.informasi-hukum.show', $dataId)->with('success', 'Data informasi hukum berhasil ditambahkan');
  }



  public function edit($id)
  {
    $title = 'Edit Data Informasi Hukum';
    $data  = InformasiHukum::findOrFail($id);

    return view('backend.informasi-hukum-form', compact(
      'title',
      'data',
    ));
  }



  public function update(Request $request, $id)
  {
    // Validate data
    $request->validate($this->validate);

    // Get data 
    $dataUpdate  = InformasiHukum::findOrFail($id);

    // Upload image if exists
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    if ($request->file('dokumen'))
      $dokumen = uploadFile($this->docDirectory, $request->file('dokumen'));

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['image']      = $image ?? $dataUpdate->image;
    $data['dokumen']    = $dokumen ?? $dataUpdate->dokumen;
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    // Update data
    $dataUpdate->update($data);


    return redirect()->route('backend.informasi-hukum.show', $id)->with('success', 'Data informasi hukum berhasil diupdate');
  }



  public function destroy($id)
  {
    // Get data and delete
    InformasiHukum::findOrFail($id)->delete();

    return redirect()->route('backend.informasi-hukum.index')->with('info', 'Data informasi hukum berhasil dihapus');
  }
}
