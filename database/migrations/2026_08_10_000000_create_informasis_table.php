<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasis', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['berita', 'infografis', 'panduan', 'riset']);
            $table->string('judul');
            $table->text('isi');
            $table->string('gambar', 255)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('penulis', 255)->nullable();
            $table->integer('tahun')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tipe', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasis');
    }
};
