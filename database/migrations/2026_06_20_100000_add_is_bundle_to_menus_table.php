<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::addColumnIfMissing('menus', 'is_bundle', function (Blueprint $table) {
            $table->boolean('is_bundle')->default(false)->after('most_ordered');
        });
    }

    public function down(): void
    {
        // Kolom dibiarkan — rollback manual jika diperlukan.
    }
};
