<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('items', function (Blueprint $table) {
        //     // Rename kolom sesuai format baru
        //     $table->renameColumn('name', 'dscription');
        //     $table->renameColumn('item_code', 'itemCode'); 
        //     $table->renameColumn('barcode', 'codeBars');
        // });
        
        // // Ubah codeBars menjadi nullable
        // Schema::table('items', function (Blueprint $table) {
        //     $table->string('codeBars')->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    //     Schema::table('items', function (Blueprint $table) {
    //         // Kembalikan nama kolom ke format lama
    //         $table->renameColumn('dscription', 'name');
    //         $table->renameColumn('itemCode', 'item_code');
    //         $table->renameColumn('codeBars', 'barcode');
    //     });
        
    //     // Kembalikan codeBars menjadi not null
    //     Schema::table('items', function (Blueprint $table) {
    //         $table->string('barcode')->nullable(false)->change();
    //     });
    }
};
