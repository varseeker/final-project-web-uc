<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafeMigration
{
    public static function createTableIfMissing(string $table, callable $callback): void
    {
        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, $callback);
    }

    public static function hasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    public static function addColumnIfMissing(string $table, string $column, callable $callback): void
    {
        if (self::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $callback);
    }

    /**
     * Hapus tabel auth POS lama (pos_users) beserta FK yang masih menempel.
     * User POS diambil dari tabel users inventory (tanpa prefix).
     */
    public static function dropLegacyPosAuthTables(): void
    {
        if (! Schema::hasTable('users') && ! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        self::dropForeignKeysToPrefixedTable('users', [
            config('laravel-cart.carts.table', 'carts'),
            'order',
            'ordered_items',
        ]);

        $connection = Schema::getConnection();
        $prefix = $connection->getTablePrefix();

        if ($connection->getDriverName() === 'pgsql') {
            if (Schema::hasTable('password_reset_tokens')) {
                DB::statement(sprintf('DROP TABLE IF EXISTS "%spassword_reset_tokens" CASCADE', $prefix));
            }

            if (Schema::hasTable('users')) {
                DB::statement(sprintf('DROP TABLE IF EXISTS "%susers" CASCADE', $prefix));
            }

            return;
        }

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }

    /**
     * @param  list<string>  $dependentTables
     */
    private static function dropForeignKeysToPrefixedTable(string $referencedTable, array $dependentTables): void
    {
        if (! Schema::hasTable($referencedTable)) {
            return;
        }

        $connection = Schema::getConnection();
        $prefix = $connection->getTablePrefix();
        $referenced = $prefix.$referencedTable;

        if ($connection->getDriverName() !== 'pgsql') {
            foreach ($dependentTables as $table) {
                if (! Schema::hasTable($table) || ! self::hasColumn($table, 'user_id')) {
                    continue;
                }

                try {
                    Schema::table($table, function (Blueprint $blueprint) {
                        $blueprint->dropForeign(['user_id']);
                    });
                } catch (\Throwable) {
                    // FK sudah tidak ada.
                }
            }

            return;
        }

        foreach ($dependentTables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $qualified = $prefix.$table;

            $constraints = DB::select(
                'SELECT con.conname AS name
                 FROM pg_constraint con
                 INNER JOIN pg_class rel ON rel.oid = con.conrelid
                 INNER JOIN pg_class frel ON frel.oid = con.confrelid
                 INNER JOIN pg_namespace nsp ON nsp.oid = rel.relnamespace
                 WHERE nsp.nspname = current_schema()
                   AND rel.relname = ?
                   AND frel.relname = ?
                   AND con.contype = \'f\'',
                [$qualified, $referenced]
            );

            foreach ($constraints as $constraint) {
                DB::statement(sprintf(
                    'ALTER TABLE "%s" DROP CONSTRAINT IF EXISTS "%s"',
                    $qualified,
                    $constraint->name
                ));
            }
        }
    }
}
