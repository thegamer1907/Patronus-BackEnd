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
        Schema::create('accounts',function (Blueprint $table) {
            $table->string('email');
            $table->integer('account_no')->unique()->unsigned();
            $table->integer('balance')->unsigned();
            $table->foreign('email')->references('email')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['email', 'account_no']);
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
