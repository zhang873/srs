<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampususer extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campususer', function(Blueprint $table)
		{
			//
			$table->increments('id')->unsigned()->index();
			$table->integer('user_id');
			$table->integer('campus_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campususer', function(Blueprint $table)
		{
			//
		    Schema::drop ( 'campususer' );
		});
	}

}
