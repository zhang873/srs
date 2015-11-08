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
			if (Schema::hasColumn('groups', 'major_classification') == false) {
				$table->tinyInteger('major_classification')->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'major') == false) {
				$table->string('major',255)->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'year') == false) {
				$table->smallInteger('year')->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'semester') == false) {
				$table->tinyInteger('semester')->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'having_stu_no') == false) {
				$table->tinyInteger('having_stu_no')->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'class_adviser') == false) {
			$table->string('class_adviser',31)->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'exam_point') == false) {
				$table->string('exam_point',127)->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'is_deleted') == false) {
				$table->tinyInteger('is_deleted')->nullable();//new
			}
			
			if (Schema::hasColumn('groups', 'campus_id') == false) {
				$table->integer('campus_id')->unsigned();//new
			}
			
			if (Schema::hasColumn('groups', 'school_id') == false) {
				$table->integer('school_id')->unsigned();//new
			}
			
			if (Schema::hasColumn('groups', 'campus_id') == false) {
				$table->foreign('campus_id')->references('id')->on('campuses');
			}
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
