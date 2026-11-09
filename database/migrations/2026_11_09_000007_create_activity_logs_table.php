<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catatan tindakan staff/admin (fitur wajib admin).
 * subject_type + subject_id menunjuk data yang diubah, misalnya
 * App\Models\Cancellation #12.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);                 // contoh: cancellation.approved, room_type.updated
            $table->string('description');                // kalimat yang tampil di halaman log
            $table->nullableMorphs('subject');            // subject_type, subject_id
            $table->json('properties')->nullable();       // data sebelum/sesudah, dll.
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
