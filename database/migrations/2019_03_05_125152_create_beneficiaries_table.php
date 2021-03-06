<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries',function (Blueprint $table) {
            $table->string('email');
            $table->integer('ben_account_no')->unsigned();
            $table->string('name');
            $table->string('ben_email');
            $table->foreign('email')->references('email')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['email', 'ben_email']);
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
        Schema::dropIfExists('beneficiaries');
    }
}
