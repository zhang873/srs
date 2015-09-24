<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAdmissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admissions', function(Blueprint $table)
		{
			//
			$table->tinyInteger('distribution')->nullable();//new
			$table->tinyInteger('is_serving')->nullable();//new
			$table->string('company_organization')->nullable();//new
			$table->string('company_address')->nullable();//new
			$table->integer('company_postcode')->nullable()->nullable();//new
			$table->string('company_phone')->nullable();//new
			$table->string('email')->nullable();//new
			$table->tinyInteger('is_deleted')->nullable();//new
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
			//
			$table->dropColumn('distribution');
			$table->dropColumn('is_serving');
			$table->dropColumn('company_organization');
			$table->dropColumn('company_address');
			$table->dropColumn('company_postcode');
			$table->dropColumn('company_phone');
			$table->dropColumn('email');
			$table->tinyInteger('is_deleted');
		});
	}

}
