<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('country');
            $table->string('administrative_area_level_1');
            $table->string('administrative_area_level_2');
            $table->string('administrative_area_level_3');
            $table->string('administrative_area_level_4');
            $table->string('administrative_area_level_5');
            $table->string('locality');
            $table->string('lat');
            $table->string('lng');
            $table->integer('zoom');
            $table->text('url');
            $table->text('formatted_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
