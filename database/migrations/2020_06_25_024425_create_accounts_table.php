<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->char('type', 1); //C=Caja B=Banco
            $table->date('date_initial_balance');
            $table->char('account_type', 1)->nullable(); //C=Corriente A=Ahorros
            $table->string('aliase', 100);
            $table->string('bank',100)->nullable();
            $table->string('number', 50)->nullable();
            $table->string('NIT', 20)->nullable();
            $table->string('holder', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->float('initial_balance',11,2);
            $table->float('credits',11,2)->unsigned();
            $table->float('debits',11,2)->unsigned();
            $table->float('balance',11,2);
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
        Schema::dropIfExists('accounts');
    }
}
