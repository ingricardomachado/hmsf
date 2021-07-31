<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('visit_type_id')->unsigned()->nullable();
            $table->foreign('visit_type_id')->references('id')->on('visit_types')->onDelete('cascade');
            $table->bigInteger('property_id')->unsigned()->nullable();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->datetime('checkin')->nullable();
            $table->datetime('checkout')->nullable();
            $table->bigInteger('visitor_id')->unsigned()->nullable();
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('cascade');
            $table->bigInteger('visiting_car_id')->unsigned()->nullable();
            $table->foreign('visiting_car_id')->references('id')->on('visiting_cars')->onDelete('cascade');
            $table->string('notes',1000)->nullable();
            $table->string('file_name',255)->nullable();
            $table->string('file_type',10)->nullable();
            $table->Integer('file_size')->unsigned()->nullable();
            $table->string('file',255)->nullable();
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
        Schema::dropIfExists('visits');
    }
}
