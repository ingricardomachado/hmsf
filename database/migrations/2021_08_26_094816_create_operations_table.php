<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('partner_id')->unsigned()->nullable();
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->Integer('number');
            $table->string('company',100)->nullable();
            $table->date('date');
            $table->string('folio',20)->nullable();
            $table->float('amount', 11,2);
            $table->float('partner_tax', 8,2);
            $table->float('customer_tax', 8,2);
            $table->float('hm_tax', 8,2);
            $table->float('partner_profit', 11,2);
            $table->float('customer_profit', 11,2);
            $table->float('hm_profit', 11,2);
            $table->string('notes',200)->nullable();
            $table->Integer('status')->default(1);
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
        Schema::dropIfExists('operations');
    }
}
