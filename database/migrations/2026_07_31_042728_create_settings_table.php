<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'petaSpasial'],
            ['key' => 'site_icon', 'value' => null],
            ['key' => 'site_favicon', 'value' => null],
            ['key' => 'hero_title', 'value' => 'petaSpasial'],
            ['key' => 'hero_subtitle', 'value' => 'Sistem Informasi Geospasial — Visualisasi, kelola, dan analisis data spasial wilayah secara interaktif.'],
            ['key' => 'hero_tagline', 'value' => 'Jelajahi Data Spasial Wilayah'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
