<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DataLampiran;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ArtikelHukumController extends Controller
{
	protected string $docDirectory;
	protected string $imgDirectory;
	protected array $validate;

	public function __construct()
	{
		$this->imgDirectory = config('app.img_directory');
		$this->docDirectory = config('app.doc_directory');
		$this->validate = [
			'jenis_peraturan'   => ['required'],
			'judul'             => ['required'],
			'tahun_terbit'      => ['required', 'numeric'],
			'tanggal_penetapan' => ['required', 'date'],
			'sumber'            => ['required'],
			'bahasa'            => ['required'],
		];
	}


	public function index(Request $request)
	{
		$title = 'Artikel / Majalah Hukum';
		$data  = DB::table('document')
			->where('tipe_dokumen', 3)
			->orderBy('document.created_at', 'desc')
			->select(
				'document.id',
				'jenis_peraturan',
				'judul',
				'tahun_terbit',
				'sumber',
			);

		if ($request->filled('jenis_peraturan'))
			$data->where('jenis_peraturan', $request->jenis_peraturan);

		if ($request->filled('judul'))
			$data->where('judul', 'like', "%$request->judul%");

		if ($request->filled('tahun_terbit'))
			$data->where('tahun_terbit', 'like', "%$request->tahun_terbit%");

		if ($request->filled('sumber'))
			$data->where('sumber', 'like', '%' . $request->sumber . '%');

		$data = $data->orderBy('document.created_at', 'desc')->paginate(15)->withQueryString();


		return view('backend.artikel-hukum', compact(
			'title',
			'data',
		));
	}


	public function show($id)
	{
		$tipeDokumen = 'artikel';
		$title       = 'Detail Artikel / Majalah Hukum';

		$data        = DB::table('document')
			->where('document.id', $id)
			->leftJoin('data_lampiran', 'document.id', '=', 'data_lampiran.id_dokumen')
			->leftJoin('user as creator', 'document._created_by', '=', 'creator.id')
			->leftJoin('user as updater', 'document._created_by', '=', 'updater.id')
			->select('document.*', 'creator.username as _created_by', 'updater.username as _updated_by', 'data_lampiran.judul_lampiran', 'data_lampiran.deskripsi_lampiran', 'data_lampiran.dokumen_lampiran')
			->first();


		return view('backend.artikel-view', compact(
			'tipeDokumen',
			'title',
			'data',
		));
	}


	public function create()
	{
		$title = 'Tambah Data Artikel / Majalah Hukum';

		return view('backend.artikel-form', compact(
			'title',
		));
	}


	public function store(Request $request)
	{
		$request->validate($this->validate);

		// Upload abstrak
		if ($request->file('abstrak'))
			$abstrak = uploadFile($this->docDirectory, $request->file('abstrak'));

		// Set data document
		$data = $request->except('_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');

		$data['tipe_dokumen']      = 3;
		$data['tanggal_penetapan'] = Carbon::createFromFormat('d-F-Y', $request->tanggal_penetapan)->format('Y-m-d');
		$data['abstrak']           = $abstrak ?? null;
		$data['created_at']        = Carbon::now();
		$data['updated_at']        = Carbon::now();

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
			$lampiran['updated_at']         = Carbon::now();

			// Store to database
			DataLampiran::create($lampiran);
		}


		return redirect()->route('backend.artikel.show', $dataId)->with('success', 'Data artikel berhasil ditambahkan.');
	}


	public function edit($id)
	{
		$title = 'Edit Data Artikel / Majalah Hukum';
		$data = Document::findOrFail($id);
		$lampiran  = DB::table('data_lampiran')->where('id_dokumen', $data->id)->first();

		return view('backend.artikel-form', compact(
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

		// Set data document
		$data = $request->except('_token', 'judul_lampiran', 'deskripsi_lampiran', 'dokumen_lampiran');

		$data['abstrak']           = $abstrak ?? $dataUpdate->abstrak;
		$data['tanggal_penetapan'] = Carbon::createFromFormat('d-F-Y', $request->tanggal_penetapan)->format('Y-m-d');
		$data['gambar_sampul']     = $gambarSampul ?? $dataUpdate->gambar_sampul;
		$data['updated_at']        = Carbon::now();

		// Update data
		$dataUpdate->update($data);


		// Process lampiran data
		if ($request->judul_lampiran) {
			// Set data lampiran
			$lampiran['judul_lampiran']     = $request->judul_lampiran;
			$lampiran['deskripsi_lampiran'] = $request->deskripsi_lampiran;
			$lampiran['updated_at']         = Carbon::now();

			$lampiranUpdate = DataLampiran::where('id_dokumen', $id)->first();

			if (!$lampiranUpdate) { // create new if not exists
				$lampiran['dokumen_lampiran'] = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));
				$lampiran['id_dokumen']       = $id;
				$lampiran['created_at']       = Carbon::now();

				DataLampiran::create($lampiran);
				// 
			} else { // update if exists
				if ($request->file('dokumen_lampiran'))
					$dokumenLampiran = uploadFile($this->docDirectory, $request->file('dokumen_lampiran'));

				$lampiran['dokumen_lampiran'] = $dokumenLampiran ?? $lampiranUpdate->dokumen_lampiran;
				$lampiranUpdate->update($lampiran);
			}
		}


		return redirect()->route('backend.artikel.show', $id)->with('success', 'Data artikel berhasil diupdate.');
	}


	public function destroy($id)
	{
		Document::findOrFail((int) $id)->delete();
		return redirect()->route('backend.artikel.index')->with('info', 'Data artikel berhasil dihapus.');
	}
}
