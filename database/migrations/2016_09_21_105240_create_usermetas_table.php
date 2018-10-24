<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsermetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('user_metas', function (Blueprint $table) {
					$table->increments('id');
					$table->string('meta_name',255);
					$table->string('meta_value',255);
					$table->integer('user_id');
			});    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_metas');
    }
}
