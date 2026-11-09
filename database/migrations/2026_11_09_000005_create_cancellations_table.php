<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengajuan pembatalan.
 * Satu booking boleh punya beberapa pengajuan (misal ditolak lalu diajukan lagi),
 * tetapi aplikasi wajib mencegah lebih dari satu pengajuan berstatus pending.
 *
 * refund_percentage dan refund_amount dihitung saat pengajuan dibuat,
 * memakai aturan di config/glamping.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();   // yang mengajukan
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedTinyInteger('refund_percentage')->default(0);   // 0, 50, atau 100
            $table->unsignedBigInteger('refund_amount')->default(0);
            $table->text('refund_note')->nullable();   // catatan administrasi refund/deposit
            $table->text('staff_note')->nullable();    // alasan staff menyetujui/menolak

            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
