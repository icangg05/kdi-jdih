<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
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
      'status'  => ['required'],
    ];
  }



  public function index(Request $request)
  {
    // Set session MySQL agar nama bulan/hari pakai Bahasa Indonesia
    DB::statement("SET lc_time_names = 'id_ID'");

    $data = DB::table('berita');

    if ($request->filled('judul'))
      $data->where('judul', 'like', '%' . $request->judul . '%');

    if ($request->filled('tanggal')) {
      $data->whereRaw("DATE_FORMAT(created_at, '%d %M %Y') LIKE ?", ['%' . $request->tanggal . '%']);
    }

    if ($request->filled('isi')) {
      $data->where('isi', 'like', '%' . $request->isi . '%');
    }

    $data = $data->orderBy('created_at', 'desc')->paginate(15)->withQueryString();


    return view('backend.berita', compact(
      'data'
    ));
  }



  public function show($id)
  {
    $title = 'Detail Berita';
    $data  = DB::table('berita')
      ->where('berita.id', $id)
      ->leftJoin('user as creator', 'berita.created_by', '=', 'creator.id')
      ->leftJoin('user as updater', 'berita.updated_by', '=', 'updater.id')
      ->select('berita.*', 'creator.username as created_by', 'updater.username as updated_by')
      ->first();


    return view('backend.berita-view', compact(
      'title',
      'data',
    ));
  }



  public function create()
  {
    $title = 'Tambah Data Berita';
    return view('backend.berita-form', compact(
      'title',
    ));
  }



  public function store(Request $request)
  {
    // Validate data
    $request->validate($this->validate);

    // Upload image if exists
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['image']      = $image ?? null;
    $data['created_at'] = now();
    $data['created_by'] = Auth::user()->id;
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    $dataId = Berita::create($data);


    return redirect()->route('backend.berita.show', $dataId)->with('success', 'Data berita berhasil ditambahkan');
  }



  public function edit($id)
  {
    $title = 'Edit Data Berita';
    $data  = Berita::findOrFail($id);

    return view('backend.berita-form', compact(
      'title',
      'data',
    ));
  }



  public function update(Request $request, $id)
  {
    // Validate data
    $request->validate($this->validate);

    // Get data 
    $dataUpdate  = Berita::findOrFail($id);

    // Upload image if exists
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['image']      = $image ?? $dataUpdate->image;
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    // Update data
    $dataUpdate->update($data);


    return redirect()->route('backend.berita.show', $id)->with('success', 'Data berita berhasil diupdate');
  }



  public function destroy($id)
  {
    // Get data and delete
    Berita::findOrFail($id)->delete();

    return redirect()->route('backend.berita.index')->with('info', 'Data berita berhasil dihapus');
  }
}
