<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disabilitas', function (Blueprint $table) {
            $table->id();
            
            // INFORMASI DASAR DOKUMEN
            $table->string('jenis_dokumen', 50);
            $table->string('judul', 500);
            $table->string('nomor_dokumen', 100);
            $table->integer('tahun');
            $table->string('tempat_penetapan', 100)->nullable();
            $table->date('tanggal_penetapan')->nullable();
            $table->string('lembaga_penetap', 200);
            $table->string('status_dokumen', 50);
            $table->string('dokumen_terkait', 200)->nullable();
            
            // KLASIFIKASI DISABILITAS
            $table->json('jenis_disabilitas')->nullable();
            $table->string('ruang_lingkup', 50);
            $table->json('sektor_kebijakan')->nullable();
            
            // KONTEN DAN DOKUMEN
            $table->text('abstrak')->nullable();
            $table->string('kata_kunci', 500)->nullable();
            $table->integer('jumlah_halaman')->nullable();
            $table->string('bahasa', 50)->nullable();
            $table->string('dokumen_utama');
            $table->string('cover')->nullable();
            $table->string('lampiran')->nullable();
            
            // METADATA TAMBAHAN
            $table->string('penulis', 200)->nullable();
            $table->string('penerbit', 200)->nullable();
            $table->string('isbn_issn', 50)->nullable();
            $table->string('doi', 100)->nullable();
            $table->string('sumber', 200)->nullable();
            $table->string('url_referensi', 500)->nullable();
            $table->date('tanggal_unggah');
            $table->string('pengunggah', 100);
            
            // HAK AKSES DAN STATUS
            $table->string('status_publikasi', 50);
            $table->string('hak_akses', 50);
            $table->text('keterangan')->nullable();
            
            // TIMESTAMPS
            $table->timestamps();
            $table->softDeletes();
            
            // INDEXES
            $table->index('jenis_dokumen');
            $table->index('tahun');
            $table->index('status_publikasi');
            $table->index('hak_akses');
            $table->index('ruang_lingkup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disabilitas');
    }
};