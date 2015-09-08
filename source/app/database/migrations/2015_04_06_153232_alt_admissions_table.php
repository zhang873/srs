<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AltAdmissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admissions', function(Blueprint $table)
		{
			//add column 'admissionid'
		    $table->string('admissionid', 100);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admissions', function(Blueprint $table)
		{
			//drop column 'admissionid'
		    $table->dropColumn('admissionid');
		});
	}

}
