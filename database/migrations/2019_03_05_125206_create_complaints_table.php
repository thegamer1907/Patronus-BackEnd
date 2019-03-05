<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints',function (Blueprint $table) {
            $table->string('email');
            $table->string('type');
            $table->string('message');
            $table->boolean('resolved');
            $table->foreign('email')->references('email')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['email', 'type', 'message']);
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
        Schema::dropIfExists('complaints');
    }
}
