<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        if (! SafeMigration::hasColumn('menus', 'inventory_menu_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->unsignedBigInteger('inventory_menu_id')->nullable()->after('id');
                $table->index('inventory_menu_id');
            });
        }

        if (! SafeMigration::hasColumn('menus', 'inventory_menu_code')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('inventory_menu_code')->nullable()->unique()->after('inventory_menu_id');
            });
        }

        if (! SafeMigration::hasColumn('menus', 'is_active')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('most_ordered');
            });
        }

        if (! SafeMigration::hasColumn('menus', 'inventory_synced_at')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->timestamp('inventory_synced_at')->nullable()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        Schema::table('menus', function (Blueprint $table) {
            $columns = [];

            foreach (['inventory_menu_id', 'inventory_menu_code', 'is_active', 'inventory_synced_at'] as $column) {
                if (SafeMigration::hasColumn('menus', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                if (SafeMigration::hasColumn('menus', 'inventory_menu_id')) {
                    $table->dropIndex(['inventory_menu_id']);
                }

                $table->dropColumn($columns);
            }
        });
    }
};
