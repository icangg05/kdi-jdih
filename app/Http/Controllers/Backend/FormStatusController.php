<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DataStatus;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormStatusController extends Controller
{
	// Function halaman create form status
	public function create($idDokumen)
	{
		$title = 'Tambah Status Peraturan';

		$selectStatus = DB::table('status')->whereIn('id', [2, 4, 6, 7])->pluck('status')
			->map(fn($status) => ['label' => ucfirst($status), 'value' => $status]);

		$selectPeraturan = Document::where('tipe_dokumen', 1)
			->where('id', '!=', (int) $idDokumen)
			->pluck('judul', 'id')
			->map(fn($judul, $id) => ['label' => $judul, 'value' => $id]);

		return view('backend.form-status', compact(
			'idDokumen',
			'title',
			'selectStatus',
			'selectPeraturan',
		));
	}


	// Function store data status
	public function store(Request $request, $idDokumen)
	{
		$request->validate([
			'status_peraturan'  => ['required'],
			'id_dokumen_target' => ['required'],
			'tanggal_perubahan' => ['required', 'date'],
		]);

		$tanggalPerubahan = Carbon::createFromFormat('d-F-Y', $request->tanggal_perubahan)->format('Y-m-d');
		$createdBy = Auth::user()->id;

		// Simpan status awal dokumen ini
		DataStatus::create([
			'id_dokumen'               => (int) $idDokumen,
			'status_peraturan'         => $request->status_peraturan,
			'id_dokumen_target'        => $request->id_dokumen_target,
			'tanggal_perubahan'        => $tanggalPerubahan,
			'catatan_status_peraturan' => trim(ucfirst($request->catatan_status_peraturan)),
			'created_at'               => now(),
			'updated_at'               => now(),
			'_created_by'              => $createdBy,
			'_updated_by'              => $createdBy,
		]);

		// Mapping status dengan data relasi dan perubahan dokumen
		$statusMap = [
			'dicabut' => [
				'status_dokumen_target' => 'mencabut',
				'status_dokumen_ini'    => 'Tidak Berlaku',
			],
			'mencabut' => [
				'status_dokumen_target' => 'dicabut',
				'status_dokumen_ini'    => 'Berlaku',
			],
			'diubah' => [
				'status_dokumen_target' => 'mengubah',
				'status_dokumen_ini'    => 'Berlaku',
			],
			'mengubah' => [
				'status_dokumen_target' => 'diubah',
				'status_dokumen_ini'    => 'Berlaku',
			],
		];

		if (!array_key_exists($request->status_peraturan, $statusMap)) {
			return redirect()->back()->withErrors([
				'status_peraturan' => 'Status peraturan tidak valid.',
			])->withInput();
		}

		$map = $statusMap[$request->status_peraturan];

		// Simpan data status pada dokumen target
		DataStatus::create([
			'id_dokumen'        => (int) $request->id_dokumen_target,
			'status_peraturan'  => $map['status_dokumen_target'],
			'id_dokumen_target' => $idDokumen,
			'tanggal_perubahan' => $tanggalPerubahan,
			'created_at'        => now(),
			'updated_at'        => now(),
			'_created_by'       => $createdBy,
			'_updated_by'       => $createdBy,
		]);

		// Update dokumen asal
		Document::findOrFail((int) $idDokumen)->update([
			'status'          => $map['status_dokumen_ini'],
			'status_terakhir' => $request->status_peraturan,
		]);

		// Update dokumen target
		Document::findOrFail((int) $request->id_dokumen_target)->update([
			'status'          => $map['status_dokumen_target'] === 'dicabut' ? 'Tidak Berlaku' : 'Berlaku',
			'status_terakhir' => $map['status_dokumen_target'],
		]);


		$tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
		$prefixRoute = checkPrefixRoute($tipeDokumen);


		return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
			'success' => 'Data status berhasil ditambahkan.',
		]);
	}


	// Function halaman edit form
	public function edit($idDokumen, $idDataStatus)
	{
		$dataStatus = DataStatus::findOrFail($idDataStatus);

		$title = 'Edit Status Peraturan';
		$selectStatus = DB::table('status')->whereIn('id', [2, 4, 6, 7])->pluck('status')
			->map(fn($status) => ['label' => ucfirst($status), 'value' => $status]);
		$selectPeraturan = Document::where('tipe_dokumen', 1)->pluck('judul', 'id')
			->map(fn($status, $id) => ['label' => $status, 'value' => $id]);

		return view('backend.form-status', compact(
			'dataStatus',
			'idDokumen',
			'title',
			'selectStatus',
			'selectPeraturan',
		));
	}


	// Function update data status
	public function update(Request $request, $idDokumen, $idDataStatus)
	{
		$request->validate([
			'status_peraturan'  => ['required'],
			'id_dokumen_target' => ['required'],
			'tanggal_perubahan' => ['required', 'date'],
		]);

		$tanggalPerubahan = Carbon::createFromFormat('d-F-Y', $request->tanggal_perubahan)->format('Y-m-d');
		$createdBy = Auth::user()->id;

		// Query data status yang diupdate
		$dataStatus = DataStatus::findOrFail((int) $idDataStatus);
		// Simpan status awal dokumen ini
		$dataStatus->update([
			'status_peraturan'         => $request->status_peraturan,
			'id_dokumen_target'        => $request->id_dokumen_target,
			'tanggal_perubahan'        => $tanggalPerubahan,
			'catatan_status_peraturan' => trim(ucfirst($request->catatan_status_peraturan)),
			'updated_at'               => now(),
			'_updated_by'              => $createdBy,
		]);

		// Mapping status dengan data relasi dan perubahan dokumen
		$statusMap = [
			'dicabut' => [
				'status_dokumen_target' => 'mencabut',
				'status_dokumen_ini'    => 'Tidak Berlaku',
			],
			'mencabut' => [
				'status_dokumen_target' => 'dicabut',
				'status_dokumen_ini'    => 'Berlaku',
			],
			'diubah' => [
				'status_dokumen_target' => 'mengubah',
				'status_dokumen_ini'    => 'Berlaku',
			],
			'mengubah' => [
				'status_dokumen_target' => 'diubah',
				'status_dokumen_ini'    => 'Berlaku',
			],
		];

		if (!array_key_exists($request->status_peraturan, $statusMap)) {
			return redirect()->back()->withErrors([
				'status_peraturan' => 'Status peraturan tidak valid.',
			])->withInput();
		}


		$map = $statusMap[$request->status_peraturan];

		// Query data status pada dokumen target
		$dataStatusTarget = DataStatus::where('id_dokumen', $request->id_dokumen_target)->first();

		// Jika dataStatusTarget null, berarti id_dokumen_target diubah, maka :
		if (!$dataStatusTarget) {
			// hapus data status pada dokumen lama
			DataStatus::where('id_dokumen', $request->id_dokumen_target_old)->delete();
			// Simpan status pada dokumen target baru
			DataStatus::create([
				'id_dokumen'        => (int) $request->id_dokumen_target,
				'status_peraturan'  => $map['status_dokumen_target'],
				'id_dokumen_target' => $idDokumen,
				'tanggal_perubahan' => $tanggalPerubahan,
				'created_at'        => now(),
				'updated_at'        => now(),
				'_created_by'       => $createdBy,
				'_updated_by'       => $createdBy,
			]);
		} else {
			// Update data status pada dokumen target
			$dataStatusTarget->update([
				'status_peraturan'  => $map['status_dokumen_target'],
				'id_dokumen_target' => $idDokumen,
				'tanggal_perubahan' => $tanggalPerubahan,
				'updated_at'        => now(),
				'_updated_by'       => $createdBy,
			]);
		}

		// Update dokumen asal
		Document::findOrFail((int) $idDokumen)->update([
			'status'          => $map['status_dokumen_ini'],
			'status_terakhir' => $request->status_peraturan,
		]);

		// Update dokumen target
		Document::findOrFail((int) $request->id_dokumen_target)->update([
			'status'          => $map['status_dokumen_target'] === 'dicabut' ? 'Tidak Berlaku' : 'Berlaku',
			'status_terakhir' => $map['status_dokumen_target'],
		]);

		$tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
		$prefixRoute = checkPrefixRoute($tipeDokumen);


		return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
			'success' => 'Data status berhasil diupdate.',
		]);
	}


	public function destroy($idDokumen, $idDataStatus)
	{
		$dataStatus = DataStatus::findOrFail($idDataStatus);

		// Hapus data status dari dokumen target jika ada
		if ($dataStatus->id_dokumen_target) {
			DataStatus::where('id_dokumen', $dataStatus->id_dokumen_target)
				->where('id_dokumen_target', $dataStatus->id_dokumen)
				->delete();
		}

		// Hapus data status utama
		$dataStatus->delete();

		$tipeDokumen = Document::find($idDokumen)->tipe_dokumen;
		$prefixRoute = checkPrefixRoute($tipeDokumen);

		return redirect()->route("backend.$prefixRoute.show", $idDokumen)->with([
			'info'      => 'Data status berhasil dihapus',
			'tabActive' => 'dataStatus',
		]);
	}
}
