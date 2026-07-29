<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class BackupDatabaseController extends Controller
{
  /** Jumlah baris yang diambil sekali jalan agar memori tetap rendah. */
  private const CHUNK = 500;

  public function index()
  {
    $name = $this->databaseName();

    // Ukuran dari information_schema: angka perkiraan yang di-cache InnoDB, bukan hasil hitung ulang.
    $stat = DB::selectOne(
      'SELECT COUNT(*) AS tables, COALESCE(SUM(data_length + index_length), 0) AS bytes
       FROM information_schema.TABLES WHERE table_schema = ?',
      [$name]
    );

    return view('backend.backup-database', [
      'title'    => 'Backup Database',
      'database' => $name,
      'size'     => Number::fileSize($stat->bytes, 2),
      'tables'   => $stat->tables,
    ]);
  }

  public function download()
  {
    $name = $this->databaseName();
    $path = storage_path('app/' . $name . '_' . now()->format('Y-m-d_His') . '.sql');

    try {
      $this->dumpTo($path);
    } catch (\Throwable $e) {
      @unlink($path);

      return back()->with('error', 'Backup gagal: ' . $e->getMessage());
    }

    return response()->download($path)->deleteFileAfterSend();
  }

  /**
   * Dump struktur + data seluruh tabel ke berkas .sql.
   *
   * ponytail: hanya BASE TABLE. View, trigger, stored procedure, dan event tidak ikut,
   * karena skema aplikasi ini tidak memakainya. Kalau nanti dipakai, tambahkan
   * SHOW CREATE VIEW / SHOW TRIGGERS di sini.
   */
  private function dumpTo(string $path): void
  {
    $pdo    = DB::getPdo();
    $handle = fopen($path, 'w');

    if ($handle === false) {
      throw new \RuntimeException('tidak bisa menulis ke ' . $path);
    }

    try {
      fwrite($handle, "-- Backup {$this->databaseName()} - " . now()->toDateTimeString() . "\n");
      fwrite($handle, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n");

      foreach ($this->baseTables() as $table) {
        // SHOW CREATE TABLE mengembalikan [Table, Create Table]; ambil kolom kedua.
        $create = array_values((array) DB::selectOne("SHOW CREATE TABLE `{$table}`"))[1];

        fwrite($handle, "\n--\n-- Tabel: {$table}\n--\n\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n{$create};\n\n");

        $this->writeRows($handle, $pdo, $table);
      }

      fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
    } finally {
      fclose($handle);
    }
  }

  /** @return string[] */
  private function baseTables(): array
  {
    // Kolom pertama = nama tabel, kolom kedua = tipe. Nama kolom pertama ikut nama database,
    // jadi diakses lewat posisi, bukan nama.
    return array_map(
      fn($row) => array_values((array) $row)[0],
      DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")
    );
  }

  private function writeRows($handle, \PDO $pdo, string $table): void
  {
    $key  = $this->primaryKey($table);
    $last = null;

    while (true) {
      if ($key === null) {
        // Tanpa primary key tunggal, paging tidak bisa dijamin urutannya, jadi ambil sekaligus.
        // ponytail: tabel semacam ini di skema ini hanya tabel pivot kecil.
        $rows = DB::table($table)->get();
      } else {
        // Keyset pagination: deterministik dan tetap cepat di baris ke sekian ratus ribu,
        // tidak seperti OFFSET yang harus melewati baris sebelumnya satu per satu.
        $rows = DB::table($table)
          ->when($last !== null, fn($q) => $q->where($key, '>', $last))
          ->orderBy($key)
          ->limit(self::CHUNK)
          ->get();
      }

      if ($rows->isEmpty()) {
        return;
      }

      $values = $rows->map(function ($row) use ($pdo) {
        $cells = array_map(
          fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
          array_values((array) $row)
        );

        return '(' . implode(',', $cells) . ')';
      });

      fwrite($handle, "INSERT INTO `{$table}` VALUES\n" . $values->implode(",\n") . ";\n");

      if ($key === null) {
        return;
      }

      $last = $rows->last()->{$key};
    }
  }

  /** Nama kolom primary key, atau null kalau tidak ada / gabungan beberapa kolom. */
  private function primaryKey(string $table): ?string
  {
    $keys = DB::select("SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'");

    return count($keys) === 1 ? $keys[0]->Column_name : null;
  }

  private function databaseName(): string
  {
    return config('database.connections.' . config('database.default') . '.database');
  }
}
