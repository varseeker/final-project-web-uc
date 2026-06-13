<?php

use App\Support\MenuOptions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->json('options')->nullable();
        });

        foreach (DB::table('menus')->orderBy('id')->get() as $menu) {
            DB::table('menus')->where('id', $menu->id)->update([
                'options' => json_encode(MenuOptions::defaultsForCategory($menu->category)),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
