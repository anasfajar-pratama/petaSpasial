<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_layers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('icon')->nullable();
            $table->string('warna_default', 9)->default('#3388ff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_layers');
    }
};
