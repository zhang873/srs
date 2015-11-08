<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampusSchool extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('campus_school') == false) {
			Schema::create('campus_school', function(Blueprint $table)
			{
				$table->increments('id');
				$table->timestamps();
				$table->integer('campus_id')->unsigned();
				$table->integer('school_id');
				
				//$table->foreign('campus_id')->references('id')->on('campuses');
				//$table->foreign('school_id')->references('school_id')->on('school_info');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campus_school');
	}

}
