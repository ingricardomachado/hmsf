<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condominium_id')->unsigned()->nullable();
            $table->foreign('condominium_id')->references('id')->on('condominiums')->onDelete('cascade');
            $table->bigInteger('supplier_category_id')->unsigned();
            $table->foreign('supplier_category_id')->references('id')->on('supplier_categories');            
            $table->string('name', 100);
            $table->string('NIT', 20)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('phone', 25);
            $table->string('email', 50)->nullable();
            $table->string('url', 50)->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
