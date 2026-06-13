<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        $cartForeignName = config('laravel-cart.carts.foreign_id', 'cart_id');
        $cartTableName = config('laravel-cart.carts.table', 'carts');

        Schema::create($table, function (Blueprint $table) use ($cartForeignName, $cartTableName) {
            $table->id();

            $table->foreignId($cartForeignName)->constrained($cartTableName)->cascadeOnDelete();
            $table->foreignId('menu_id')->references('id')->on('menus')->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);

            $table->string('variant')->nullable();
            $table->string('size')->nullable();
            $table->string('ice')->nullable();
            $table->string('sugar')->nullable();
            $table->unsignedInteger('subtotal')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');

        Schema::dropIfExists($table);
    }
};
