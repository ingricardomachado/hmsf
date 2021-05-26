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
            $table->date('start');
            $table->date('end');
            $table->date('real_start');
            $table->date('real_end');
            $table->float('budget',11,2);
            $table->integer('advance');
            $table->char('status', 1)->default('P'); //P=Pendiente C=Curso F=Finalizado
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
