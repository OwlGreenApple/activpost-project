<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('proxies', function (Blueprint $table) {
					$table->increments('id');
					$table->string('proxy',100);
					$table->string('cred',100);
					$table->string('port',100);
					$table->boolean('auth');
					$table->dateTime('created');
			});    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('proxies');
    }
}
