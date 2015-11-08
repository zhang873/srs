<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTransactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_student_transactions', function(Blueprint $table)
		{
			// create table schema
			$table->increments('id')->unsigned()->index();
			$table->integer('chargeitem');
			$table->integer('paymentmethod')->default(0);
			$table->integer('chargeyear');
			$table->string('chargesemester', 2);
			$table->integer('admission_id');
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
		Schema::table('mod_fin_student_transactions', function(Blueprint $table)
		{
			// drop the table
			Schema::drop('mod_fin_student_transactions');
		});
	}

}
