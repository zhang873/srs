<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModFinSystemsettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_systemsettings', function(Blueprint $table)
		{
		    // create table mod_fin_systemsettings
		    $table->increments('id')->unsigned()->index();
		    $table->integer('year');
		    $table->string('semester', 2);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mod_fin_systemsettings', function(Blueprint $table)
		{
		    // drop the table mod_fin_systemsettings
		    Schema::drop ( 'mod_fin_systemsettings' );
		});
	}

}
