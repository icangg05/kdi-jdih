<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
  protected string $docDirectory;
  protected string $imgDirectory;
  protected array $validate;

  public function __construct()
  {
    $this->imgDirectory = config('app.img_directory');
    $this->docDirectory = config('app.doc_directory');
    $this->validate     = [
      'tanggal' => ['required', 'date'],
      'judul'   => ['required'],
      'tag'     => ['required'],
      'isi'     => ['required'],
      'image'   => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'],
      'dokumen' => ['nullable', 'mimes:pdf', 'max:20480'],
      'status'  => ['required'],
    ];
  }



  public function index(Request $request)
  {
    // Set session MySQL agar nama bulan/hari pakai Bahasa Indonesia
    DB::statement("SET lc_time_names = 'id_ID'");

    $data = DB::table('pengumuman');

    if ($request->filled('judul'))
      $data->where('judul', 'like', '%' . $request->judul . '%');

    if ($request->filled('tanggal')) {
      $data->whereRaw("DATE_FORMAT(created_at, '%d %M %Y') LIKE ?", ['%' . $request->tanggal . '%']);
    }

    if ($request->filled('isi')) {
      $data->where('isi', 'like', '%' . $request->isi . '%');
    }

    if ($request->filled('tag')) {
      $data->where('tag', 'like', '%' . $request->tag . '%');
    }

    $data = $data->orderBy('created_at', 'desc')->paginate(15)->withQueryString();


    return view('backend.pengumuman', compact(
      'data'
    ));
  }



  public function show($id)
  {
    $pengumuman = DB::table('pengumuman')
      ->join('user as creator', 'pengumuman.created_by', '=', 'creator.id')
      ->join('user as updater', 'pengumuman.updated_by', '=', 'updater.id')
      ->where('pengumuman.id', (int) $id)
      ->select('pengumuman.*', 'creator.username as created_by', 'updater.username as updated_by')
      ->first();

    return view('backend.pengumuman-view', compact(
      'pengumuman'
    ));
  }



  public function create()
  {
    $title = 'Tambah Data Pengumuman';
    return view('backend.pengumuman-form', compact('title'));
  }



  public function store(Request $request)
  {
    $request->validate($this->validate);

    $tanggal = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');

    // Upload image and document
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    if ($request->file('dokumen'))
      $dokumen = uploadFile($this->docDirectory, $request->file('dokumen'));

    $dataId = Pengumuman::create([
      'tanggal'    => $tanggal,
      'judul'      => trim($request->judul),
      'tag'        => trim($request->tag),
      'isi'        => trim($request->isi),
      'image'      => $image ?? '',
      'dokumen'    => $dokumen ?? '',
      'status'     => (int) $request->status,
      'created_at' => Carbon::now(),
      'created_by' => Auth::user()->id,
      'updated_at' => Carbon::now(),
      'updated_by' => Auth::user()->id,
    ]);

    return redirect()->route('backend.pengumuman.show', $dataId)->with('success', 'Data pengumuman berhasil ditambahkan');
  }



  public function edit($id)
  {
    $title      = 'Edit Data Pengumuman';
    $pengumuman = DB::table('pengumuman')->where('id', (int) $id)->first();
    abort_if(!$pengumuman, 404);

    return view('backend.pengumuman-form', compact(
      'title',
      'pengumuman',
    ));
  }



  public function update(Request $request, $id)
  {
    $request->validate($this->validate);

    $tanggal    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $pengumuman = Pengumuman::findOrFail((int) $id);

    // Upload image and document
    if ($request->file('image'))
      $image = uploadFile($this->imgDirectory, $request->file('image'));

    if ($request->file('dokumen'))
      $dokumen = uploadFile($this->docDirectory, $request->file('dokumen'));

    $pengumuman->update([
      'tanggal'    => $tanggal,
      'judul'      => trim($request->judul),
      'tag'        => trim($request->tag),
      'isi'        => trim($request->isi),
      'image'      => $image ?? $pengumuman->image,
      'dokumen'    => $dokumen ?? $pengumuman->dokumen,
      'status'     => (int) $request->status,
      'updated_at' => Carbon::now(),
      'updated_by' => Auth::user()->id,
    ]);

    return redirect()->route('backend.pengumuman.show', $id)->with('success', 'Data pengumuman berhasil diupdate');
  }



  public function destroy($id)
  {
    Pengumuman::findOrFail((int) $id)->delete();

    return redirect()->route('backend.pengumuman.index')->with('info', 'Data pengumuman berhasil dihapus');
  }
}
