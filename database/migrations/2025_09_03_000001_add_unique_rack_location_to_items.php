<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Tambah unique constraint ke kolom rack_location yang sudah ada
            $table->unique('rack_location');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Hapus unique constraint
            $table->dropUnique(['rack_location']);
        });
    }
};
