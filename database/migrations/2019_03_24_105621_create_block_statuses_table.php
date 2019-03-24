<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_no')->unique()->unsigned();
            $table->foreign('account_no')->references('account_no')->on('accounts')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status');
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
        Schema::dropIfExists('block_statuses');
    }
}
