<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
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
}
