<?php

use App\Support\MenuOptions;
use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::addColumnIfMissing('menus', 'options', function (Blueprint $table) {
            $table->json('options')->nullable();
        });

        if (! SafeMigration::hasColumn('menus', 'options')) {
            return;
        }

        foreach (DB::table('menus')->orderBy('id')->get() as $menu) {
            if ($menu->options !== null) {
                continue;
            }

            DB::table('menus')->where('id', $menu->id)->update([
                'options' => json_encode(MenuOptions::defaultsForCategory($menu->category)),
            ]);
        }
    }

    public function down(): void
    {
        if (! SafeMigration::hasColumn('menus', 'options')) {
            return;
        }

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
