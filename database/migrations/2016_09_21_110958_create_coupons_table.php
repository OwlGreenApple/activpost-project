<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('coupons', function (Blueprint $table) {
					$table->increments('id');
					$table->string('coupon_code',255);
					$table->double('coupon_value',15,0);
					$table->integer('coupon_percent')->nullable();
					$table->dateTime('valid_until');
					$table->integer('package_id')->nullable();
					$table->integer('user_id')->nullable();
					$table->boolean('visible');
			});    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coupons');
    }
}
