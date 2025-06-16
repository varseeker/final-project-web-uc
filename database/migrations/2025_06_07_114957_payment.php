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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('order');

            $table->integer('totalPay');
            $table->enum('method', ['Cash', 'QRIS']);
            $table->enum('status', ['waiting-payment', 'expired', 'success']);
            $table->string('reference');
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
        $table->foreign('order_id')->references('id')->on('order');
        Schema::dropIfExists($table);
    }
};
