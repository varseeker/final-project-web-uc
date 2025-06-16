<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        $cartForeignName = config('laravel-cart.carts.foreign_id', 'cart_id');
        $cartTableName = config('laravel-cart.carts.table', 'carts');

        Schema::create($table, function (Blueprint $table) use ($cartForeignName, $cartTableName) {
            $table->id();

            $table->foreignId($cartForeignName)->constrained($cartTableName)->cascadeOnDelete();
            // $table->morphs('itemable'); // itemable_id & itemable_type
            $table->foreignId('menu_id')->references('id')->on('menus')->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);

            // Tambahan atribut khusus untuk POS
            $table->string('variant')->nullable();
            $table->string('size')->nullable();
            $table->string('ice')->nullable();
            $table->string('sugar')->nullable();
            // harga total (qty * price)
            $table->unsignedInteger('subtotal')->nullable(); 

            $table->timestamps();
        });

         // Trigger BEFORE INSERT
        DB::unprepared('
            CREATE TRIGGER calc_subtotal_cart_items_before_insert
            BEFORE INSERT ON cart_items
            FOR EACH ROW
            BEGIN
                DECLARE menu_price DECIMAL(10,2);
                SELECT price INTO menu_price
                FROM menus
                WHERE id = NEW.menu_id
                LIMIT 1;
                
                SET NEW.subtotal = NEW.quantity * menu_price;
            END
        ');

        // Trigger BEFORE UPDATE
        DB::unprepared('
            CREATE TRIGGER calc_subtotal_cart_items_before_update
            BEFORE UPDATE ON cart_items
            FOR EACH ROW
            BEGIN
                DECLARE menu_price DECIMAL(10,2);
                SELECT price INTO menu_price
                FROM menus
                WHERE id = NEW.menu_id
                LIMIT 1;
                
                SET NEW.subtotal = NEW.quantity * menu_price;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        
        $table->dropForeign('menu_id');
        $table->foreign('menu_id')->references('id')->on('menus');
        Schema::dropIfExists($table);
        
        DB::unprepared('DROP TRIGGER IF EXISTS calc_subtotal_cart_items_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS calc_subtotal_cart_items_before_update');
    }
};
