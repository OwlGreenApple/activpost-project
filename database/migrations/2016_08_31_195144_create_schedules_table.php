<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
						$table->integer('user_id')->nullable();
            $table->string('image')->nullable();
            $table->string('slug')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('media_type')->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('delay')->default(60);
            $table->timestamp('publish_at')->nullable();
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
        Schema::drop('schedules');
    }
}
