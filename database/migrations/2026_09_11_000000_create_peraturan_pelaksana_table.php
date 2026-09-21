<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peraturan_pelaksana', function (Blueprint $table) {
            $table->id();
            $table->integer('id_dokumen')->index();
            // Peraturan pelaksana berupa dokumen yang sudah ada (id) atau berkas yang
            // diunggah langsung (judul + file); salah satunya saja yang terisi.
            // Tabel sejenis (`peraturan_terkait`) menyimpan id sebagai varchar, di sini integer.
            $table->integer('peraturan_pelaksana')->nullable();
            $table->string('judul_pelaksana')->nullable();
            $table->string('file_pelaksana')->nullable();
            $table->string('catatan_pelaksana')->nullable();
            $table->integer('urutan')->nullable();
            $table->timestamps();
            $table->string('_created_by')->nullable();
            $table->string('_updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peraturan_pelaksana');
    }
};
