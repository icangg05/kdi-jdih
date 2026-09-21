<?php

namespace App\Services;

use App\Models\Berita;
use App\Models\Document;
use App\Models\InformasiHukum;
use App\Models\Narasi;
use App\Models\Pengumuman;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * File gambar dari editor Quill (storage/editor). HTML hanya menyimpan URL-nya.
 *
 * - Data diupdate: gambar yang dibuang/diganti dari isi langsung dihapus.
 * - Data dihapus: semua gambar di isinya ikut dihapus.
 * - Diupload tapi form tak jadi disimpan: disapu prune() saat upload berikutnya.
 * Gambar yang masih dirujuk konten lain (mis. hasil salin-tempel) tidak dihapus.
 */
class EditorImages
{
	public const DIRECTORY = 'editor';

	// Batas ukuran gambar (KB), dipakai validasi server & cek di browser.
	// Jangan melebihi upload_max_filesize PHP (server: 2M).
	public const MAX_KB = 2048;

	// Semua kolom yang diisi editor Quill. Form baru yang memakai editor wajib
	// didaftarkan di sini — kalau tidak, gambarnya dianggap tak terpakai.
	public const COLUMNS = [
		Narasi::class         => ['text', 'translations'],
		Berita::class         => ['isi', 'translations'],
		Pengumuman::class     => ['isi', 'translations'],
		InformasiHukum::class => ['isi', 'translations'],
		Document::class       => ['amar_status', 'translations'], // putusan
	];


	public static function register(): void
	{
		foreach (self::COLUMNS as $model => $columns) {
			$model::updated(function ($row) use ($columns) {
				if ($row->wasChanged($columns))
					self::deleteUnused(self::paths(Arr::only($row->getOriginal(), $columns)));
			});

			$model::deleted(fn ($row) => self::deleteUnused(self::paths(Arr::only($row->getAttributes(), $columns))));
		}
	}


	// Sapu file yang tak dirujuk konten mana pun. File berumur < 1 hari dilewati —
	// bisa jadi sedang dipakai di form yang belum disimpan.
	// ponytail: jalan setiap ada upload (belum ada scheduler); pindah ke schedule harian bila ada.
	public static function prune(): void
	{
		$cutoff = now()->subDay()->getTimestamp();

		self::deleteUnused(array_filter(
			Storage::allFiles(self::DIRECTORY),
			fn ($file) => Storage::lastModified($file) < $cutoff,
		));
	}


	/** Path storage (editor/...) dari URL gambar di HTML; juga di JSON translations (\/). */
	public static function paths(array $contents): array
	{
		preg_match_all('#/storage/(' . self::DIRECTORY . '/[\w/-]+\.\w+)#', str_replace('\/', '/', implode(' ', $contents)), $m);

		return array_unique($m[1]);
	}


	private static function deleteUnused(array $paths): void
	{
		if (!$paths) return;

		$used = self::used();
		Storage::delete(array_filter($paths, fn ($path) => !str_contains($used, basename($path))));
	}


	// Seluruh konten editor yang memuat gambar editor, dari semua tabel terdaftar.
	private static function used(): string
	{
		$used = '';
		foreach (self::COLUMNS as $model => $columns) {
			$table = (new $model)->getTable();
			foreach ($columns as $column) {
				$used .= DB::table($table)->where($column, 'like', '%' . self::DIRECTORY . '%')->pluck($column)->implode(' ');
			}
		}

		return $used;
	}
}
