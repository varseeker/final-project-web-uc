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
        Schema::create('order', function (Blueprint $table){
            $table->id();
            $table->integer('total')->nullable();
            $table->integer('amountPaid')->nullable();
            $table->integer('amountChange')->nullable();
            $table->string('customer')->nullable();
            $table->enum('status', ['waiting-payment', 'void', 'paid']);
            $table->enum('payment-status', ['pending', 'expired', 'failed', 'success']);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('payReference')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($table);
        $table->dropForeign('user_id');
        $table->foreign('user_id')->references('id')->on('menus');
    }
};
