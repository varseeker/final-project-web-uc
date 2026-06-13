<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        $cartForeignName = config('laravel-cart.carts.foreign_id', 'cart_id');
        $cartTableName = config('laravel-cart.carts.table', 'carts');
        $prefixedCartItems = $this->prefixed($table);
        $prefixedMenus = $this->prefixed('menus');

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

        DB::unprepared("
            CREATE TRIGGER calc_subtotal_cart_items_before_insert
            BEFORE INSERT ON {$prefixedCartItems}
            FOR EACH ROW
            BEGIN
                DECLARE menu_price DECIMAL(10,2);
                SELECT price INTO menu_price
                FROM {$prefixedMenus}
                WHERE id = NEW.menu_id
                LIMIT 1;

                SET NEW.subtotal = NEW.quantity * menu_price;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER calc_subtotal_cart_items_before_update
            BEFORE UPDATE ON {$prefixedCartItems}
            FOR EACH ROW
            BEGIN
                DECLARE menu_price DECIMAL(10,2);
                SELECT price INTO menu_price
                FROM {$prefixedMenus}
                WHERE id = NEW.menu_id
                LIMIT 1;

                SET NEW.subtotal = NEW.quantity * menu_price;
            END
        ");
    }

    public function down(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        $prefixedCartItems = $this->prefixed($table);

        DB::unprepared('DROP TRIGGER IF EXISTS calc_subtotal_cart_items_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS calc_subtotal_cart_items_before_update');

        Schema::dropIfExists($table);
    }

    private function prefixed(string $table): string
    {
        return DB::getTablePrefix().$table;
    }
};
