<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->string('name',100);
            $table->string('NIT',15)->nullable();
            $table->string('position',200)->nullable();
            $table->string('address',500)->nullable();
            $table->string('phone',15)->nullable();
            $table->string('cell',15);
            $table->string('email',50)->nullable();
            $table->string('notes',500)->nullable();
            $table->string('avatar_name',100)->nullable();
            $table->string('avatar_type',10)->nullable();
            $table->Integer('avatar_size')->unsigned()->nullable();
            $table->string('avatar',100)->nullable();
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
        Schema::dropIfExists('employees');
    }
}
