<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invited_by_user_id');
            $table->integer('calendar_id');
            $table->boolean('calendar_admin')->default(false);
            $table->string('url_key', 60)->nullable();
            $table->string('email', 60);
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
		Schema::dropIfExists("calendar_invitations");
    }
}
