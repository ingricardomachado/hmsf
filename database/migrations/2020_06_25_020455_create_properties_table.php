<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 10);
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('aux_id')->unsigned()->nullable();
            $table->foreign('aux_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('tax',8,2)->default(0);
            $table->float('due_debt',11,2)->default(0);
            $table->float('debt',11,2)->default(0);
            $table->char('status',1); //S=Solvente P=Pendiente M=Moroso
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
        Schema::dropIfExists('properties');
    }
}
