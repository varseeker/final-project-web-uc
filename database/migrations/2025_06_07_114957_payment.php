<?php

use App\Support\SafeMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SafeMigration::createTableIfMissing('payment', function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
