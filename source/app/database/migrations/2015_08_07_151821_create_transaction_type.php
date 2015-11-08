<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_transaction_type', function(Blueprint $table)
		{
			// create table transaction type
			$table->increments('id')->unsigned()->index();
			$table->string('name');
			$table->string('sysid');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mod_fin_transaction_type', function(Blueprint $table)
		{
			// delete the table
			Schema::drop('mod_fin_transaction_type');
		});
	}

}
