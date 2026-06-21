<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menus') || ! SafeMigration::hasColumn('menus', 'category')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        $table = $connection->getTablePrefix().'menus';

        if ($driver === 'pgsql') {
            DB::statement(sprintf(
                'ALTER TABLE "%s" ALTER COLUMN category TYPE VARCHAR(20) USING category::text',
                $table
            ));
        } elseif ($driver === 'mysql') {
            DB::statement(sprintf(
                'ALTER TABLE `%s` MODIFY category VARCHAR(20) NULL',
                $table
            ));
        }

        DB::table('menus')->where('category', 'Snack')->update(['category' => 'Makanan']);
        DB::table('menus')->whereIn('category', ['Coffee', 'Non-coffee'])->update(['category' => 'Minuman']);
    }

    public function down(): void
    {
        // Rollback manual jika diperlukan.
    }
};
