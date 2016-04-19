<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiCalendarWorkerJoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_calendar_worker_joins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendar_id');
            $table->integer('worker_id');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists("api_calendar_worker_joins");
    }
}
