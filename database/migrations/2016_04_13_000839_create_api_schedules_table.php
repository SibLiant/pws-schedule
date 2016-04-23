<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendar_id');
            $table->integer('user_id');
            $table->jsonb('json_data');
			$table->integer('parent_id')->nullable();
			$table->boolean('active')->default(true);
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->useCurrent();
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
		Schema::drop('api_schedules');
    }
}
