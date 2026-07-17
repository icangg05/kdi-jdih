<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembentukan_puu', function (Blueprint $table) {
            $table->id();
            
            // Informasi Dasar
            $table->string('jenis_dokumen', 50);
            $table->string('judul', 500);
            $table->string('nomor_dokumen', 100)->nullable();
            $table->year('tahun');
            $table->string('lembaga_pemrakarsa', 200);
            $table->string('status_dokumen', 50);
            $table->string('tahapan_pembentukan', 50)->nullable();
            
            // Field Spesifik berdasarkan jenis dokumen
            // Naskah Akademik
            $table->text('rumusan_masalah')->nullable();
            $table->text('tujuan_penelitian')->nullable();
            $table->text('metodologi_penelitian')->nullable();
            $table->string('tim_penyusun', 300)->nullable();
            $table->date('tanggal_penyelesaian')->nullable();
            
            // Rancangan PUU
            $table->string('jenis_rancangan', 50)->nullable();
            $table->year('prolegnas')->nullable();
            $table->string('inisiator', 50)->nullable();
            $table->string('pansus_panja', 200)->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            
            // Penelitian Hukum
            $table->text('latar_belakang')->nullable();
            $table->text('fokus_penelitian')->nullable();
            $table->text('hasil_penelitian')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->string('lokasi_penelitian', 200)->nullable();
            
            // Pengkajian Hukum
            $table->text('objek_pengkajian')->nullable();
            $table->string('jenis_pengkajian', 50)->nullable();
            $table->text('tujuan_pengkajian')->nullable();
            $table->text('kesimpulan_pengkajian')->nullable();
            $table->date('tanggal_pengkajian')->nullable();
            
            // Pengkajian Konstitusi
            $table->text('aspek_konstitusi')->nullable();
            $table->string('jenis_pengkajian_konstitusi', 50)->nullable();
            $table->text('dasar_hukum_pengkajian')->nullable();
            $table->string('instansi_pengkaji', 200)->nullable();
            $table->text('implikasi_konstitusional')->nullable();
            
            // Analisis Evaluasi
            $table->text('objek_evaluasi')->nullable();
            $table->string('metode_evaluasi', 50)->nullable();
            $table->text('indikator_evaluasi')->nullable();
            $table->text('temuan_evaluasi')->nullable();
            $table->text('rekomendasi_perbaikan')->nullable();
            $table->date('periode_evaluasi')->nullable();
            
            // Informasi Umum
            $table->text('abstrak')->nullable();
            $table->text('kata_kunci')->nullable();
            $table->string('penulis', 300)->nullable();
            $table->string('editor', 300)->nullable();
            
            // Upload Dokumen
            $table->string('dokumen_utama')->nullable();
            $table->string('cover')->nullable();
            $table->string('lampiran')->nullable();
            
            // Pengelolaan Dokumen
            $table->string('status_publikasi', 20)->default('draft');
            $table->string('hak_akses', 20)->default('private');
            $table->string('kategori', 50)->nullable();
            $table->text('keterangan')->nullable();
            
            // Metadata
            $table->string('pengunggah', 100)->default('admin');
            $table->date('tanggal_unggah')->default(now());
            $table->integer('jumlah_download')->default(0);
            $table->integer('views')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk pencarian
            $table->index('jenis_dokumen');
            $table->index('status_publikasi');
            $table->index('hak_akses');
            $table->index('tahun');
            $table->index('lembaga_pemrakarsa');
            $table->fulltext(['judul', 'abstrak', 'kata_kunci']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembentukan_puu');
    }
};