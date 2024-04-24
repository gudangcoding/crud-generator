<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_trx',);
            $table->timestamps();
            $table->softDeletes(); // Menambahkan soft deletes


        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelians');
    }
};
