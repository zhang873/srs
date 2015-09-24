<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpelDropoutRecovery extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if (Schema::hasTable('student_expel') == false)
		{
			Schema::create('student_expel', function (blueprint $table) {
				$table->engine = 'InnoDB';
				$table->increments('id')->unsigned()->index();
		
				$table->smallinteger('expel_year');
				$table->tinyinteger('expel_semester');
				$table->string('cause',255);
				$table->string('document_id',127);
				$table->string('remark',255);
				$table->tinyinteger('is_deleted');
				$table->timestamps();
		
				$table->string('student_id',15);

		
			});
		}
		
		if (Schema::hasTable('student_dropout') == false)
		{
			Schema::create('student_dropout', function (blueprint $table) {
				$table->engine = 'InnoDB';
				$table->increments('id')->unsigned()->index();
		
				$table->smallinteger('application_year');
				$table->tinyinteger('application_semester');
				$table->string('cause',255);
				$table->tinyinteger('approval_result_ province');
				$table->string('approval_suggestion_province',255);
				$table->tinyinteger('is_deleted');
				$table->timestamps();
		
				$table->string('student_id',15);
		
			});
		}
		
		if (Schema::hasTable('student_recovery') == false)
		{
			Schema::create('student_recovery', function (blueprint $table) {
				$table->engine = 'InnoDB';
				$table->increments('id')->unsigned()->index();
		
				$table->smallinteger('recovery_year');
				$table->tinyinteger('recovery_semester');
				//$table->tinyinteger('student_status');
				$table->tinyinteger('approval_result');
				$table->string('remark',255);
				$table->tinyinteger('is_deleted');
				$table->timestamps();
		
				$table->string('student_id',15);
		
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
		Schema::drop ( 'student_expel');
		Schema::drop ( 'student_dropout');
		Schema::drop ( 'student_recovery');
	}

}
