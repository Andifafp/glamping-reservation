<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reservasi. Satu booking = satu jenis kamar (bisa beberapa unit).
 *
 * Status:
 *  - pending   : sudah dibuat, belum dibayar (unit sudah ditahan)
 *  - confirmed : sudah dibayar
 *  - cancelled : pembatalan disetujui staff (unit dilepas)
 *  - completed : tamu sudah check-out
 *
 * price_per_night disalin dari room_types saat booking dibuat, supaya
 * perubahan tarif di kemudian hari tidak mengubah total booking lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();     // contoh: GLM-8K2P4Q
            // Reservasi wajib login, jadi setiap booking pasti punya pemilik.
            // restrictOnDelete: akun yang punya booking tidak bisa dihapus,
            // admin cukup menonaktifkan (is_active = false).
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();

            // Data kontak saat reservasi (default diisi dari profil user,
            // boleh diubah). Dipakai juga untuk cek booking.
            $table->string('contact_name', 100);
            $table->string('contact_email');
            $table->string('contact_phone', 20);

            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('nights');
            $table->unsignedSmallInteger('units');
            $table->unsignedTinyInteger('guests');

            // Rincian biaya (Rp)
            $table->unsignedInteger('price_per_night');
            $table->unsignedBigInteger('subtotal');           // price_per_night x nights x units
            $table->boolean('with_insurance')->default(false);
            $table->unsignedBigInteger('insurance_fee')->default(0);
            $table->unsignedBigInteger('total_price');        // subtotal + insurance_fee

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // Mempercepat pengecekan ketersediaan per rentang tanggal
            $table->index(['room_type_id', 'status', 'check_in', 'check_out']);
            $table->index('contact_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
