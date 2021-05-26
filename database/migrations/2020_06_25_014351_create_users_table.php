<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->char('role',3); //SAM - ADM - US1 - US2
            $table->string('cell',15)->nullable();
            $table->char('iso2',2)->nullable();
            $table->string('num_region',5)->nullable();
            $table->string('phone',15)->nullable();
            $table->boolean('committee')->default(false);
            $table->boolean('email_notification')->default(false);
            $table->string('avatar',100)->nullable();
            $table->string('avatar_name',100)->nullable();
            $table->string('avatar_type',10)->nullable();
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
        Schema::dropIfExists('users');
    }
}
