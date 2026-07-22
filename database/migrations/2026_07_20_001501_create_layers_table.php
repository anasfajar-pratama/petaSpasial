<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->foreignId('kategori_id')->constrained('kategori_layers')->cascadeOnDelete();
            $table->string('geom_type', 20); // Point, LineString, Polygon, MultiPolygon
            $table->string('warna', 9)->default('#3388ff');
            $table->string('icon_marker', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('opacity', 3, 2)->default(1.00);
            $table->integer('order')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
