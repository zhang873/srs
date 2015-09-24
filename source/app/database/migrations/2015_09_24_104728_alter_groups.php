<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('groups', function(Blueprint $table)
		{
			//
			$table->tinyInteger('major_classification')->nullable();//new
			$table->string('major',255)->nullable();//new
			$table->smallInteger('year')->nullable();//new
			$table->tinyInteger('semester')->nullable();//new
			$table->tinyInteger('having_stu_no')->nullable();//new
			$table->string('class_adviser',31)->nullable();//new
			$table->string('exam_point',127)->nullable();//new
			$table->tinyInteger('is_deleted')->nullable();//new
			
			$table->integer('campus_id')->unsigned();//new
			$table->integer('school_id')->unsigned();//new
			
			$table->foreign('campus_id')->references('id')->on('campuses');
			//$table->foreign('school_id')->references('school_id')->on('school_info');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropColumn('major_classification');
			$table->dropColumn('major');
			$table->dropColumn('year');
			$table->dropColumn('semester');
			$table->dropColumn('having_stu_no');
			$table->dropColumn('class_adviser');
			$table->dropColumn('exam_point');
			$table->dropColumn('is_deleted');
			$table->dropColumn('campus_id');
			$table->dropColumn('school_id');
		});
	}

}
