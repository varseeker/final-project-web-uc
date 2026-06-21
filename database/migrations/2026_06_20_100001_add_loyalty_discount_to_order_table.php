<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::addColumnIfMissing('order', 'subtotal', function (Blueprint $table) {
            $table->unsignedInteger('subtotal')->nullable()->after('total');
        });

        SafeMigration::addColumnIfMissing('order', 'loyalty_discount_percent', function (Blueprint $table) {
            $table->unsignedTinyInteger('loyalty_discount_percent')->default(0)->after('subtotal');
        });

        SafeMigration::addColumnIfMissing('order', 'loyalty_discount_amount', function (Blueprint $table) {
            $table->unsignedInteger('loyalty_discount_amount')->default(0)->after('loyalty_discount_percent');
        });
    }

    public function down(): void
    {
        // Kolom dibiarkan — rollback manual jika diperlukan.
    }
};
