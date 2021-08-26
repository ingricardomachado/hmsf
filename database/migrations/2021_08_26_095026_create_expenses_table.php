<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expense_type_id')->unsigned()->nullable();
            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('cascade');
            $table->bigInteger('center_id')->unsigned()->nullable();
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->date('date');
            $table->string('concept',100);
            $table->float('amount',11,2);
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
        Schema::dropIfExists('expenses');
    }
}
