<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublishFieldsToScheduleAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_account', function (Blueprint $table) {
					if (!Schema::hasColumn('schedule_account', 'published_time')) {
						$table->timestamp('published_time')->nullable();
						$table->timestamp('deleted_time')->nullable();
					}
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_account', function (Blueprint $table) {
            //
        });
    }
}
