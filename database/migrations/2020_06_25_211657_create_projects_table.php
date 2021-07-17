<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('description', 250);
            $table->date('planned');
            $table->date('planned_end');
            $table->date('started')->nullable();
            $table->date('finished')->nullable();
            $table->float('budget',11,2)->nullable();
            $table->integer('advance')->default(0);
            $table->char('status', 1)->default('P'); //P=Pendiente E=Ejecucion F=Finalizado C=Cancelado
            $table->string('hash',35)->nullable();
            $table->string('created_by', 100)->nullable();
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
        Schema::dropIfExists('projects');
    }
}
