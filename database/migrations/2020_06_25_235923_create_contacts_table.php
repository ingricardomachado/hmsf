<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->string('name',100);
            $table->string('company',200)->nullable();
            $table->string('position',200)->nullable();
            $table->string('address',500)->nullable();
            $table->string('phone',50)->nullable();
            $table->string('cell',50);
            $table->string('email',50)->nullable();
            $table->string('about',500)->nullable();
            $table->string('twitter', 25)->nullable();
            $table->string('facebook', 50)->nullable();
            $table->string('instagram', 50)->nullable();
            $table->string('avatar_name',100)->nullable();
            $table->string('avatar_type',10)->nullable();
            $table->Integer('avatar_size')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
