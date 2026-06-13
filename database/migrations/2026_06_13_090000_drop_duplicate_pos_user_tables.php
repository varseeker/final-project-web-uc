<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus tabel auth POS lama dari deploy gagal (pos_users, pos_password_reset_tokens).
     * Data user utama ada di tabel users inventory (tanpa prefix).
     */
    public function up(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }

    public function down(): void
    {
        // Tidak dibuat ulang — user dikelola oleh Inventory Management System.
    }
};
