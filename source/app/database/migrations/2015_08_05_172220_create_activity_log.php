<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_log', function(Blueprint $table)
		{
			// create activity log table
		    $table->increments('id');
		    $table->integer('user_id')->nullable();
		    $table->string('text');
		    $table->timestamp('created_at');
		    $table->string('user_agent');
		    $table->string('ip_address', 64);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_log', function(Blueprint $table)
		{
			// drop activity log table
		    Schema::drop('activity_log');
		});
	}

}
