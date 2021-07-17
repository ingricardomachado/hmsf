<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_photo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('title',100)->nullable();
            $table->string('file_name',255)->nullable();
            $table->string('file_type',10)->nullable();
            $table->string('file',255)->nullable();
            $table->integer('file_size')->unsigned()->nullable();            
            $table->char('stage',2)->default('AN'); //AN=Antes, DU=Durante, DE=Despuest
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_photo');
    }
}
