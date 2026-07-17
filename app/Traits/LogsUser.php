<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LogsUser
{
	public function getControllerName(): string
	{
		// Gunakan `tipe_dokumen` jika tersedia, kalau tidak pakai nama model
		if (property_exists($this, 'tipe_dokumen')) {
			return match ($this->tipe_dokumen) {
				1 => 'Peraturan',
				2 => 'Monografi',
				3 => 'Artikel',
				default => 'Putusan',
			};
		}

		// Ambil nama class
		$modelName = class_basename($this);

		// Hilangkan prefix 'Data' (case-sensitive)
		$modelName = preg_replace('/^Data/', '', $modelName);

		return $this->convertPascalToTitle($modelName);
	}
	

	protected function convertPascalToTitle(string $string): string
	{
		// Tambahkan spasi sebelum huruf kapital yang diikuti huruf kecil
		$spaced = preg_replace('/(?<!^)([A-Z][a-z])/', ' $1', $string);

		// Tambahkan spasi sebelum huruf kapital yang diikuti huruf kapital lain
		$spaced = preg_replace('/(?<!^)([A-Z]+)([A-Z][a-z])/', ' $1 $2', $spaced);

		return trim($spaced);
	}
	

	public function logUser(string $aksiPrefix, $idDokumen): void
	{
		$controller = $this->getControllerName();
		$aksi       = "{$aksiPrefix} {$controller}";
		$username   = Auth::user()?->username ?? 'guest';
		$userId     = Auth::id() ?? 0;

		DB::table('log_pustakawan')->insert([
			'controller' => $controller,
			'aksi'       => $aksi,
			'dokumen_id' => $idDokumen,
			'keterangan' => textLog(strtolower("{$aksiPrefix} data {$controller}"), $username),
			'created_at' => now(),
			'updated_at' => now(),
			'created_by' => $userId,
			'updated_by' => $userId,
		]);
	}
}
