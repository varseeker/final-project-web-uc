<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rekening', function (Blueprint $table) {
            $table->string('nomor_rekening')->primary();
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('saldo', 15, 2);
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening');
    }
};
