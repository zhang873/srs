<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentSelectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        //学生选课信息表
        if (Schema::hasTable('student_selection') == false) {
            Schema::create('student_selection', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('student_id')->unsigned()->index();;
                $table->integer('course_id')->unsigned()->index();;
                $table->smallInteger('year')->unsigned();
                $table->tinyInteger('semester')->unsigned();
                $table->tinyInteger('is_obligatory');
                $table->float('expense');
                $table->tinyInteger('selection_status');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('admissions');
                $table->foreign('course_id')->references('id')->on('course');

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
        Schema::drop('student_selection');
	}

}
