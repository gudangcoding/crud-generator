<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;
            use Ramsey\Uuid\Uuid;

            class CreateTesTable extends Migration
            {
                public function up()
                {
                    Schema::create('tess', function (Blueprint $table) {
$table->uuid('id')->primary();
$table->timestamps();
$table->softDeletes(); // Menambahkan soft deletes
$table->string('tes', );
$table->string('bnbn', );

                    });

                }

                public function down()
                {
                    Schema::dropIfExists('tess');

                }
            }