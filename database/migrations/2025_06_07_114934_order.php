<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::createTableIfMissing('order', function (Blueprint $table) {
            $table->id();
            $table->integer('total')->nullable();
            $table->integer('amountPaid')->nullable();
            $table->integer('amountChange')->nullable();
            $table->string('customer')->nullable();
            $table->enum('status', ['waiting-payment', 'void', 'paid']);
            $table->enum('payment-status', ['pending', 'expired', 'failed', 'success']);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('payReference')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
