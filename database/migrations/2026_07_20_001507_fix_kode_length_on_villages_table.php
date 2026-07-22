<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->string('kode', 15)->change();
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->string('kode', 10)->change();
        });
    }
};
