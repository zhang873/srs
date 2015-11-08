<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModFinRecever extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_recever', function(Blueprint $table)
		{
			// create table mod_fin_recever
			$table->increments('id')->unsigned()->index();
			$table->string('name');
			$table->integer('campus_id')->default(0);
			$table->timestamp('created_at');
			$table->timestamp('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mod_fin_recever', function(Blueprint $table)
		{
			// delete the table
			Schema::drop('mod_fin_recever');
		});
	}

}
