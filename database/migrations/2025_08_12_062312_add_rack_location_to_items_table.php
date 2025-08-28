<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'name')) {
                $table->renameColumn('name', 'dscription');
            }
            if (Schema::hasColumn('items', 'item_code')) {
                $table->renameColumn('item_code', 'itemCode');
            }
            if (Schema::hasColumn('items', 'barcode')) {
                $table->renameColumn('barcode', 'codeBars');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'dscription')) {
                $table->renameColumn('dscription', 'name');
            }
            if (Schema::hasColumn('items', 'itemCode')) {
                $table->renameColumn('itemCode', 'item_code');
            }
            if (Schema::hasColumn('items', 'codeBars')) {
                $table->renameColumn('codeBars', 'barcode');
            }
        });
    }
};
