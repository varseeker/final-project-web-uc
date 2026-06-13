<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Hapus tabel auth POS lama dari deploy gagal (pos_users, pos_password_reset_tokens).
     * Data user utama ada di tabel users inventory (tanpa prefix).
     */
    public function up(): void
    {
        SafeMigration::dropLegacyPosAuthTables();
    }

    public function down(): void
    {
        // Tidak dibuat ulang — user dikelola oleh Inventory Management System.
    }
};
