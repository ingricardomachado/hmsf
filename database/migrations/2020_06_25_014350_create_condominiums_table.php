<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCondominiumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condominiums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('type',1); //R=Residencial C=Comercial
            $table->bigInteger('property_type_id')->unsigned()->nullable();
            $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
            $table->string('name', 100);
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->string('city')->nullable();
            $table->string('address',400)->nullable();
            $table->integer('max_properties')->unsigned();
            $table->String('contact');
            $table->String('phone', 15)->nullable();
            $table->String('cell', 15);
            $table->String('email', 100);
            $table->char('money_format',3)->default('PC2');
            $table->string('coin',10)->default('$');
            $table->char('status',1)->default('S'); //S=Solvente P=Pendiente M=Moroso
            $table->boolean('demo')->default(true);
            $table->integer('remaining_days')->default(30);
            $table->boolean('create_notification')->default(true);
            $table->boolean('update_notification')->default(true);
            $table->boolean('delete_notification')->default(true);
            $table->string('logo',100)->nullable();
            $table->string('logo_name',100)->nullable();
            $table->string('logo_type',10)->nullable();
            $table->integer('logo_size')->unsigned()->nullable();
            $table->boolean('active')->default(true);            
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
        Schema::dropIfExists('condominiums');
    }
}
