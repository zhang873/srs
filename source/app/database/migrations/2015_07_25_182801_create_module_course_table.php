<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('module_course') == false) {
            Schema::create('module_course', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('teaching_plan_module_id')->unsigned()->index();
                $table->integer('course_id')->unsigned()->index();
                $table->tinyinteger('is_obligatory');
                $table->smallinteger('suggested_semester')->unsigned();
                $table->tinyinteger('is_masked');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('teaching_plan_module_id')->references('id')->on('teaching_plan_module');
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
		Schema::drop('module_course');
	}

}
