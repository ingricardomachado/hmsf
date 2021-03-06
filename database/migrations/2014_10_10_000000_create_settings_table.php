<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company', 100);
            $table->string('NIT', 20);
            $table->string('address', 200);
            $table->string('phone', 25);
            $table->string('email', 50);
            $table->string('coin',10);
            $table->char('money_format',3);
            $table->float('tax', 8,2);
            $table->string('logo',100)->nullable();
            $table->string('logo_name',100)->nullable();
            $table->string('logo_type',10)->nullable();
            $table->integer('logo_size')->unsigned()->nullable();
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
        Schema::drop('settings');
    }
}
