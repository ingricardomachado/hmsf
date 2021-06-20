<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('property_id')->unsigned()->nullable();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->bigInteger('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->bigInteger('fee_id')->unsigned()->nullable();
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('cascade');
            $table->date('date');
            $table->string('title');
            $table->string('description')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('rent')->default(false);
            $table->float('day_cost',11,2)->nullable();
            $table->float('hour_cost',11,2)->nullable();
            $table->integer('tot_hours')->nullable();
            $table->float('amount',11,2);
            $table->date('confirm_date')->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('observations', 500)->nullable();
            $table->char('status',1)->default('P'); //P=Pendiente A=Aprobado R=Rechazado
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
        Schema::dropIfExists('reservations');
    }
}
