<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_transactions', function(Blueprint $table)
		{
			// create mod_fin_transactions table
			$table->increments('id')->unsigned()->index();
			$table->integer('chargeitem');
			$table->integer('paymentmethod');
			$table->integer('chargeyear');
			$table->string('chargesemester', 2);
			$table->integer('campus_id');
			$table->integer('user_id');
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
		Schema::table('mod_fin_transactions', function(Blueprint $table)
		{
			// delete the table
			Schema::drop('mod_fin_transactions');
		});
	}

}
