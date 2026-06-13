<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_menu_id')->nullable()->after('id');
            $table->string('inventory_menu_code')->nullable()->unique()->after('inventory_menu_id');
            $table->boolean('is_active')->default(true)->after('most_ordered');
            $table->timestamp('inventory_synced_at')->nullable()->after('updated_at');

            $table->index('inventory_menu_id');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['inventory_menu_id']);
            $table->dropColumn([
                'inventory_menu_id',
                'inventory_menu_code',
                'is_active',
                'inventory_synced_at',
            ]);
        });
    }
};
