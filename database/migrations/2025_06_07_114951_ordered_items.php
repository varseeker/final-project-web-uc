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
        Schema::create('ordered_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->foreignId('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('variant');
            $table->string('size');
            $table->string('ice');
            $table->string('sugar');

            // harga total (qty * price)
            $table->unsignedInteger('subtotal'); 
            $table->enum('status', ['waiting-payment', 'Ordered', 'In Process', 'Done']);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropForeign('order_id');
        $table->foreign('order_id')->references('id')->on('menus');
        $table->dropForeign('menu_id');
        $table->foreign('menu_id')->references('id')->on('menus');
        $table->dropForeign('user_id');
        $table->foreign('user_id')->references('id')->on('menus');
        Schema::dropIfExists($table);
    }
};
