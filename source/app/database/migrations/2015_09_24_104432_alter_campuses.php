<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCampuses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campuses', function(Blueprint $table)
		{
			//
			$table->integer('status_changing');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campuses', function(Blueprint $table)
		{
			//
			$table->dropColumn('status_changing');
		});
	}

}
