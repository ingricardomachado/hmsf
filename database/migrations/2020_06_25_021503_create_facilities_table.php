<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('rules', 1000)->nullable();
            $table->time('start');
            $table->time('end');
            $table->boolean('defaulters')->default(true);
            $table->boolean('rent')->default(false);
            $table->float('day_cost',8,2)->default(0)->unsigned();
            $table->float('hour_cost',8,2)->default(0)->unsigned();
            $table->char('status', 1)->default('O');
            $table->string('photo',100)->nullable();
            $table->string('photo_name',100)->nullable();
            $table->string('photo_type',10)->nullable();
            $table->Integer('photo_size')->unsigned()->nullable();
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
        Schema::dropIfExists('facilities');
    }
}
