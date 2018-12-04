<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_account', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->string('msg')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('schedule_account');
    }
}
