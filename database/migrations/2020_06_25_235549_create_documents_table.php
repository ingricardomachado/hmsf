<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('document_type_id')->unsigned()->nullable();
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
            $table->string('name',100);
            $table->string('description',500)->nullable();
            $table->string('file_name',255);
            $table->string('file_type',10);
            $table->Integer('file_size')->nullable();
            $table->string('file',255);
            $table->boolean('public')->default(true);
            $table->string('created_by',100);
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
        Schema::dropIfExists('documents');
    }
}
