<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $userForeignName = config('laravel-cart.users.foreign_id', 'user_id');
        $table = config('laravel-cart.carts.table', 'carts');

        SafeMigration::createTableIfMissing($table, function (Blueprint $table) use ($userForeignName) {
            $table->id();
            $table->unsignedBigInteger($userForeignName)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $table = config('laravel-cart.carts.table', 'carts');

        Schema::dropIfExists($table);
    }
};
