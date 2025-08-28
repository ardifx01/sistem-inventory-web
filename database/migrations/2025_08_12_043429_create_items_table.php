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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('dscription');                 // sebelumnya name
            $table->string('itemCode')->unique();         // sebelumnya item_code
            $table->string('codeBars')->unique();         // sebelumnya barcode
            $table->text('description')->nullable();      // kalau masih butuh deskripsi panjang lain
            $table->string('rack_location')->nullable();  // tambahkan biar konsisten sama controller
            $table->timestamps();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
