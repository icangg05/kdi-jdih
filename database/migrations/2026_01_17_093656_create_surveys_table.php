<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('instansi')->nullable();
            $table->string('jenis_pengguna'); // Mahasiswa, Akademisi, Praktisi Hukum, Masyarakat Umum, Lainnya
            $table->integer('kemudahan_akses'); // 1-5
            $table->integer('kelengkapan_informasi'); // 1-5
            $table->integer('kecepatan_loading'); // 1-5
            $table->integer('tampilan_antarmuka'); // 1-5
            $table->integer('relevansi_pencarian'); // 1-5
            $table->text('saran_perbaikan')->nullable();
            $table->text('fitur_harapan')->nullable();
            $table->boolean('bersedia_dihubungi')->default(false);
            $table->string('kontak')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surveys');
    }
};