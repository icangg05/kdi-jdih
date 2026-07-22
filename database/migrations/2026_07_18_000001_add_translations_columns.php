<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // Tabel konten publik yang butuh terjemahan (kolom sidecar).
  private array $tables = ['document', 'berita', 'pengumuman', 'informasi_hukum', 'narasi'];

  public function up(): void
  {
    foreach ($this->tables as $table) {
      if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'translations')) {
        Schema::table($table, function (Blueprint $t) {
          // text (bukan json) supaya aman di MyISAM & dump SQL lama.
          // utf8mb4 wajib: tabel warisan dump berkolasi latin1, tak bisa
          // menyimpan aksara Mandarin/Korea tanpa ini.
          $t->text('translations')->nullable()
            ->charset('utf8mb4')
            ->collation('utf8mb4_unicode_ci');
        });
      }
    }
  }

  public function down(): void
  {
    foreach ($this->tables as $table) {
      if (Schema::hasTable($table) && Schema::hasColumn($table, 'translations')) {
        Schema::table($table, fn(Blueprint $t) => $t->dropColumn('translations'));
      }
    }
  }
};
