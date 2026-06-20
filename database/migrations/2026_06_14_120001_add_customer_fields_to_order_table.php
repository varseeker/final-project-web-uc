<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::addColumnIfMissing('order', 'customer_id', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('customer')->index();
        });

        SafeMigration::addColumnIfMissing('order', 'loyalty_points_earned', function (Blueprint $table) {
            $table->unsignedInteger('loyalty_points_earned')->nullable()->after('customer_id');
        });
    }

    public function down(): void
    {
        // Kolom dibiarkan — rollback manual jika diperlukan.
    }
};
