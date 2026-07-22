<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('layer_id')->constrained('layers')->cascadeOnDelete();
            $table->string('format', 20); // geojson, csv, shp, kml
            $table->string('filename');
            $table->integer('total_imported')->default(0);
            $table->integer('total_duplicates')->default(0);
            $table->integer('total_errors')->default(0);
            $table->text('errors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
