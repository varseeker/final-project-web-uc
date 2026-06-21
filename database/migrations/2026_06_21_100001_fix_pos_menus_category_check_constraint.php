<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan production: constraint enum lama (pos_menus_category_check) masih
 * aktif setelah migrasi kategori sehingga Makanan/Minuman ditolak PostgreSQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menus') || ! SafeMigration::hasColumn('menus', 'category')) {
            return;
        }

        $connection = Schema::getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            return;
        }

        $table = $connection->getTablePrefix().'menus';

        DB::statement(sprintf(
            'ALTER TABLE "%s" DROP CONSTRAINT IF EXISTS pos_menus_category_check',
            $table
        ));

        $constraints = DB::select(
            'SELECT con.conname AS name
             FROM pg_constraint con
             INNER JOIN pg_class rel ON rel.oid = con.conrelid
             INNER JOIN pg_namespace nsp ON nsp.oid = rel.relnamespace
             WHERE nsp.nspname = current_schema()
               AND rel.relname = ?
               AND con.contype = \'c\'',
            [$table]
        );

        foreach ($constraints as $constraint) {
            if (! str_contains($constraint->name, 'category')) {
                continue;
            }

            DB::statement(sprintf(
                'ALTER TABLE "%s" DROP CONSTRAINT IF EXISTS "%s"',
                $table,
                $constraint->name
            ));
        }

        DB::statement(sprintf(
            'ALTER TABLE "%s" ALTER COLUMN category TYPE VARCHAR(20) USING category::text',
            $table
        ));

        DB::table('menus')->where('category', 'Snack')->update(['category' => 'Makanan']);
        DB::table('menus')->whereIn('category', ['Coffee', 'Non-coffee'])->update(['category' => 'Minuman']);
    }

    public function down(): void
    {
        // Rollback manual jika diperlukan.
    }
};
