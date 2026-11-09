<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jenis kamar glamping (pengganti "alat camping" untuk Kelas B).
 * total_units = jumlah unit fisik yang bisa disewa (stok).
 * Harga disimpan dalam rupiah (bilangan bulat, tanpa desimal).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->json('facilities')->nullable();          // ["Kasur queen", "Kamar mandi dalam", ...]
            $table->unsignedTinyInteger('capacity');         // jumlah tamu per unit
            $table->unsignedSmallInteger('total_units');     // stok unit
            $table->unsignedInteger('price_per_night');      // tarif per malam (Rp)
            $table->string('image')->nullable();             // path file di storage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
