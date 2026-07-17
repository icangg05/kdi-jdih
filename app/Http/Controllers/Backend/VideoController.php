<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
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
      'link'    => ['required'],
    ];
  }


  public function index(Request $request)
  {
    // Set session MySQL agar nama bulan/hari pakai Bahasa Indonesia
    DB::statement("SET lc_time_names = 'id_ID'");

    $data = DB::table('video');

    if ($request->filled('judul'))
      $data->where('judul', 'like', '%' . $request->judul . '%');

    if ($request->filled('tanggal')) {
      $data->whereRaw("DATE_FORMAT(created_at, '%d %M %Y') LIKE ?", ['%' . $request->tanggal . '%']);
    }

    if ($request->filled('link')) {
      $data->where('link', 'like', '%' . $request->link . '%');
    }

    $data = $data->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

    return view('backend.video', compact(
      'data'
    ));
  }



  public function show($id)
  {
    $title = 'Detail Video';
    $data  = DB::table('video')
      ->where('video.id', $id)
      ->leftJoin('user as creator', 'video.created_by', '=', 'creator.id')
      ->leftJoin('user as updater', 'video.updated_by', '=', 'updater.id')
      ->select('video.*', 'creator.username as created_by', 'updater.username as updated_by')
      ->first();
    abort_if(!$data, 404);


    return view('backend.video-view', compact(
      'title',
      'data',
    ));
  }



  public function create()
  {
    $title = 'Tambah Data Video';
    return view('backend.video-form', compact(
      'title',
    ));
  }



  public function store(Request $request)
  {
    // Validate data
    $request->validate($this->validate);

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['created_at'] = now();
    $data['created_by'] = Auth::user()->id;
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    $dataId = Video::create($data);


    return redirect()->route('backend.video.show', $dataId)->with('success', 'Data video berhasil ditambahkan');
  }



  public function edit($id)
  {
    $title = 'Edit Data Video';
    $data  = Video::findOrFail($id);

    return view('backend.video-form', compact(
      'title',
      'data',
    ));
  }



  public function update(Request $request, $id)
  {
    // Validate data
    $request->validate($this->validate);

    // Get data 
    $dataUpdate  = Video::findOrFail($id);

    // Set data
    $data               = $request->except('_token');
    $data['tanggal']    = Carbon::createFromFormat('d-F-Y', $request->tanggal)->format('Y-m-d');
    $data['updated_at'] = now();
    $data['updated_by'] = Auth::user()->id;

    // Update data
    $dataUpdate->update($data);


    return redirect()->route('backend.video.show', $id)->with('success', 'Data video berhasil diupdate');
  }



  public function destroy($id)
  {
    // Get data and delete
    Video::findOrFail($id)->delete();

    return redirect()->route('backend.video.index')->with('info', 'Data video berhasil dihapus');
  }
}
