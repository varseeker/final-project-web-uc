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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('nomor_rekening_asal');
            $table->string('nomor_rekening_tujuan');
            $table->string('bank_tujuan');
            $table->enum('jenis_transaksi', ['Top Up/Beli', 'Payment/Jual', 'Transfer']);
            $table->decimal('jumlah_transaksi', 15, 2);
            $table->timestamps();
            $table->foreign('nomor_rekening_asal')->references('nomor_rekening')->on('rekening')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
