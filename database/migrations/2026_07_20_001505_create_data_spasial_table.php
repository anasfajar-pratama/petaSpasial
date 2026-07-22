<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_spasial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layer_id')->constrained('layers')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_layers')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('status', 50)->default('aktif');
            $table->integer('tahun')->nullable();
            $table->decimal('luas', 15, 4)->nullable();
            $table->text('foto')->nullable();
            $table->text('dokumen')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE data_spasial ADD COLUMN geometry geometry(Geometry, 4326)');
        DB::statement('CREATE INDEX idx_data_spasial_geometry ON data_spasial USING GIST (geometry)');
        DB::statement('CREATE INDEX idx_data_spasial_layer_id ON data_spasial (layer_id)');
        DB::statement('CREATE INDEX idx_data_spasial_kategori_id ON data_spasial (kategori_id)');
        DB::statement('CREATE INDEX idx_data_spasial_district_id ON data_spasial (district_id)');
        DB::statement('CREATE INDEX idx_data_spasial_village_id ON data_spasial (village_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('data_spasial');
    }
};
