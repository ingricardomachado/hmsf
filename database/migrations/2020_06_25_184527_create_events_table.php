<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('property_id')->unsigned()->nullable();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->bigInteger('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->date('date');
            $table->string('title');
            $table->string('description');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->boolean('all_day')->default(true);
            $table->float('day_cost',11,2)->nullable();
            $table->float('hour_cost',11,2)->nullable();
            $table->integer('tot_hrs')->nullable();
            $table->float('amount',11,2);
            $table->string('bgcolor')->nullable();
            $table->boolean('public')->default(true);
            $table->char('status',1)->default('P'); //P=Pendiente C=Confirmado F=Finalizado
            $table->date('date_confirm');
            $table->string('comment', 400);
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
        Schema::dropIfExists('events');
    }
}
