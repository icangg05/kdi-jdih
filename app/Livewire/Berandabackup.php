<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Beranda extends Component
{
	public function render()
	{
		// Data pejabat kota kendari
		$pejabat = [
			[
				'nama'    => 'dr. Hj. SISKA KARINA IMRAN, SKM',
				'jabatan' => 'Wali Kota Kendari',
				'gambar'  => "assets/img/1.jpg",
			],
			[
				'nama'    => 'SUDIRMAN',
				'jabatan' => 'Wakil Wali Kota Kendari',
				'gambar'  => "assets/img/2.jpg",
			],
			[
				'nama'    => 'AMIR HASAN, STP, SH, M.Si',
				'jabatan' => 'PJ Sekretaris Daerah Kota Kendari',
				'gambar'  => "assets/img/3.jpg",
			],
		];

		// Query data narasi
		$narasi = DB::table('narasi')->first();

		// Query data peraturan
		$peraturan = DB::table('document')
			->where('tipe_dokumen', 1)
			->orderBy('created_at', 'desc')
			->limit(4)
			->get();

		// Query data monografi
		$monografi =  DB::table('document')
			->where('tipe_dokumen', 2)
			->orderBy('created_at', 'desc')
			->first();
		$dataPengarang = DB::table('data_pengarang')
			->where('id_dokumen', $monografi->id)
			->pluck('nama_pengarang');
		$subjek    = DB::table('data_subyek')->where('id_dokumen', $monografi->id)->get();
		$pengarang = DB::table('pengarang')->whereIn('id', $dataPengarang)->get();

		// Query data pengumuman
		$pengumuman = DB::table('pengumuman')
			->orderBy('created_at', 'desc')
			->limit(3)
			->get();

		// Query data video
		$video = DB::table('video')
			->orderBy('created_at', 'desc')
			->limit(3)
			->get();

		// Query data berita
		$berita = DB::table('berita')
			->where('status', 1)
			->orderBy('created_at', 'desc')
			->limit(3)
			->get();

		// Query count peraturan, monografi, artikel, putusan
		$countPeraturan = DB::table('document')
			->where('tipe_dokumen', 1)
			->count();
		$countMonografi = DB::table('document')
			->where('tipe_dokumen', 2)
			->count();
		$countArtikel = DB::table('document')
			->where('tipe_dokumen', 3)
			->count();
		$countPutusan = DB::table('document')
			->where('tipe_dokumen', 4)
			->count();


		return view('livewire.beranda', compact(
			'pejabat',
			'narasi',
			'peraturan',
			'monografi',
			'subjek',
			'pengarang',
			'pengumuman',
			'video',
			'berita',
			'countPeraturan',
			'countMonografi',
			'countArtikel',
			'countPutusan',
		));
	}
}
