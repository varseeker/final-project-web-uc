<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama');
            $table->string('jenis');
            $table->text('deskripsi');
            $table->decimal('suku_bunga', 5, 2);
            $table->decimal('minimum_saldo', 15, 2);
            $table->decimal('biaya_admin', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produk');
    }
}
