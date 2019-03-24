<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_account_no')->unsigned();
            $table->integer('to_account_no')->unsigned();
            $table->foreign('from_account_no')->references('account_no')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('to_account_no')->references('account_no')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('amount')->unsigned();
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
        Schema::dropIfExists('transactions');
    }
}
