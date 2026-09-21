<?php

namespace Tests\Feature;

use App\Models\PeraturanPelaksana;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Memastikan berkas unggahan peraturan pelaksana tidak tertinggal di storage.
class PeraturanPelaksanaBerkasTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // Kunci ke SQLite in-memory: env Docker (DB_DATABASE) tidak ditimpa phpunit.xml,
    // dan migration lengkap butuh tabel legacy MySQL, jadi tabelnya dibuat di sini saja.
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    DB::purge('sqlite');

    Schema::create('peraturan_pelaksana', function (Blueprint $table) {
      $table->id();
      $table->integer('id_dokumen');
      $table->integer('peraturan_pelaksana')->nullable();
      $table->string('judul_pelaksana')->nullable();
      $table->string('file_pelaksana')->nullable();
      $table->string('catatan_pelaksana')->nullable();
      $table->timestamps();
    });
    Schema::create('log_pustakawan', function (Blueprint $table) {
      $table->id();
      $table->string('controller');
      $table->string('aksi');
      $table->integer('dokumen_id');
      $table->text('keterangan');
      $table->integer('created_by');
      $table->integer('updated_by');
      $table->timestamps();
    });

    Storage::fake();
  }

  private function berkas(string $nama): string
  {
    Storage::put(config('app.doc_directory') . $nama, '%PDF');
    return $nama;
  }

  public function test_berkas_lama_terhapus_saat_diganti(): void
  {
    $row = PeraturanPelaksana::create(['id_dokumen' => 1, 'judul_pelaksana' => 'A', 'file_pelaksana' => $this->berkas('lama.pdf')]);

    $row->update(['file_pelaksana' => $this->berkas('baru.pdf')]);

    Storage::assertMissing('dokumen/lama.pdf');
    Storage::assertExists('dokumen/baru.pdf');
  }

  public function test_berkas_terhapus_saat_sumber_diubah_ke_pilih_peraturan(): void
  {
    $row = PeraturanPelaksana::create(['id_dokumen' => 1, 'judul_pelaksana' => 'A', 'file_pelaksana' => $this->berkas('a.pdf')]);

    $row->update(['peraturan_pelaksana' => 5, 'judul_pelaksana' => null, 'file_pelaksana' => null]);

    Storage::assertMissing('dokumen/a.pdf');
  }

  public function test_berkas_tetap_saat_hanya_catatan_diubah(): void
  {
    $row = PeraturanPelaksana::create(['id_dokumen' => 1, 'judul_pelaksana' => 'A', 'file_pelaksana' => $this->berkas('a.pdf')]);

    $row->update(['catatan_pelaksana' => 'Revisi']);

    Storage::assertExists('dokumen/a.pdf');
  }

  public function test_berkas_terhapus_saat_baris_dihapus(): void
  {
    $row = PeraturanPelaksana::create(['id_dokumen' => 1, 'judul_pelaksana' => 'A', 'file_pelaksana' => $this->berkas('a.pdf')]);

    $row->delete();

    Storage::assertMissing('dokumen/a.pdf');
  }

  public function test_hapus_baris_tanpa_berkas_tidak_menghapus_folder_dokumen(): void
  {
    $lain = $this->berkas('milik-dokumen-lain.pdf');
    $row  = PeraturanPelaksana::create(['id_dokumen' => 1, 'peraturan_pelaksana' => 5]);

    $row->delete();

    Storage::assertExists('dokumen/' . $lain);
  }
}
