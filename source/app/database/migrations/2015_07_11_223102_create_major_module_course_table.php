<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorModuleCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //专业模块拥有的课程
        Schema::create('major_module_course', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments ('id');
            $table->integer ('program_id')->unsigned();
            $table->integer ('module_id')->unsigned();
            $table->integer ('course_id')->unsigned()->nullable();
            $table->tinyinteger ('is_obligatory');
            $table->smallinteger ('suggested_semester')->unsigned();
            $table->tinyinteger ('is_masked');
            $table->tinyInteger ('is_deleted')->default(0);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('program_info');
            $table->foreign('module_id')->references('id')->on('major_module_info');
            $table->foreign('course_id')->references('id')->on('course');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('major_module_course');
	}

}
