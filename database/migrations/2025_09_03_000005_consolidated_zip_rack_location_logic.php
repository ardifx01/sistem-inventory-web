<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus constraint lama jika ada
        try {
            Schema::table('items', function (Blueprint $table) {
                $table->dropUnique(['rack_location']);
            });
        } catch (\Exception $e) {
            // Constraint tidak ada, abaikan error
        }

        // Update semua nilai null atau empty menjadi ZIP
        DB::table('items')
            ->whereNull('rack_location')
            ->orWhere('rack_location', '')
            ->update(['rack_location' => 'ZIP']);

        // Tambah kolom untuk unique constraint yang mengecualikan ZIP
        if (!Schema::hasColumn('items', 'rack_location_unique')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('rack_location_unique')->nullable()->after('rack_location');
            });
        }

        // Update kolom baru: null untuk ZIP, nilai asli untuk yang lain
        DB::statement("UPDATE items SET rack_location_unique = CASE WHEN rack_location = 'ZIP' THEN NULL ELSE rack_location END");

        // Tambah unique constraint pada kolom baru
        try {
            Schema::table('items', function (Blueprint $table) {
                $table->unique('rack_location_unique');
            });
        } catch (\Exception $e) {
            // Unique constraint sudah ada, abaikan
        }
    }

    public function down(): void
    {
        // Hapus kolom dan constraint baru
        if (Schema::hasColumn('items', 'rack_location_unique')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropUnique(['rack_location_unique']);
                $table->dropColumn('rack_location_unique');
            });
        }
        
        // Kembalikan constraint lama jika tidak ada ZIP duplikat
        $zipCount = DB::table('items')->where('rack_location', 'ZIP')->count();
        if ($zipCount <= 1) {
            Schema::table('items', function (Blueprint $table) {
                $table->unique('rack_location');
            });
        }
    }
};
