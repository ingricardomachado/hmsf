<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('from_account_id')->unsigned()->nullable();
            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->bigInteger('to_account_id')->unsigned()->nullable();
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->date('date');
            $table->string('concept',200);
            $table->char('payment_method',2);
            $table->string('reference',50);            
            $table->float('amount',11,2)->unsigned();
            $table->string('file',100)->nullable();
            $table->string('file_name',100)->nullable();
            $table->string('file_type',10)->nullable();
            $table->integer('file_size')->unsigned()->nullable();
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
        Schema::dropIfExists('transfers');
    }
}
