<?php

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
			$table->date('scheduled_date');
			$table->integer('worker_id');
			$table->integer('user_id');
			$table->foreign('worker_id')->references('id')->on('workers');
			$table->integer('project_id');
			$table->foreign('project_id')->references('id')->on('projects');
			$table->integer('job_length_days');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('schedule_note', 500)->nullable();
			$table->string('external_link', 200)->nullable();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop("schedules");
    }
}
