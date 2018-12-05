<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idaff', function (Blueprint $table) {
            $table->increments('id');
						$table->string('trans_id')->nullable();
						$table->string('name')->nullable();
						$table->string('email')->nullable();
						$table->string('phone')->nullable();
						$table->string('status')->nullable();
						$table->double('grand_total',15,0)->nullable();
						$table->boolean('executed')->nullable();
						$table->string('invoice')->nullable();
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
        Schema::dropIfExists('idaff');
    }
}
