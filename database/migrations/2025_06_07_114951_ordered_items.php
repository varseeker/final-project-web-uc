<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::createTableIfMissing('ordered_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->foreignId('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('variant');
            $table->string('size');
            $table->string('ice');
            $table->string('sugar');
            $table->unsignedInteger('subtotal');
            $table->enum('status', ['waiting-payment', 'Ordered', 'In Process', 'Done']);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordered_items');
    }
};
