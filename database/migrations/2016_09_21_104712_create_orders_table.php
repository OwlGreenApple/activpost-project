<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_order',255);
						$table->integer('month');
            $table->string('order_type',50);
            $table->string('order_status',50);
            $table->string('image')->nullable();
            $table->double('base_price',15,0);
            $table->double('sub_price',15,0);
            $table->double('affiliate',15,0);
            $table->double('discount',15,0);
            $table->double('total',15,0);
						$table->integer('package_id');
						$table->integer('user_id');
						$table->integer('coupon_id')->nullable();
						$table->timestampsTz();
        });    
		}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
