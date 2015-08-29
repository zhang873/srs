<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        //省课程总表
        if (Schema::hasTable('course') == false) {
            Schema::create('course', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('code', 5);
                $table->string('name');
                $table->string('abbreviation');
                $table->tinyInteger('credit')->unsigned();
                $table->integer('credit_hour')->unsigned();
                $table->tinyInteger('is_practice')->unsigned()->default(0);
                $table->string('lecturer');
                $table->tinyInteger('is_certification')->default(0);
                $table->date('define_date');
                $table->longText('remark')->nullable();
                $table->integer('department_id')->unsigned()->index();
                $table->tinyInteger('classification');
                $table->tinyInteger('state')->default(1);
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();
                $table->foreign('department_id')->references('id')->on('department_info');
            });
        }

        //省学期开设的课程
        if (Schema::hasTable('course_establish_province') == false) {
            Schema::create('course_establish_province', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->smallInteger('year')->unsigned();
                $table->tinyInteger('semester')->unsigned();
                $table->integer('course_id')->unsigned()->index();
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('course');
            });
        }

        //教学点学期开设的课程
        if (Schema::hasTable('course_establish_campus') == false) {
            Schema::create('course_establish_campus', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->smallInteger('year')->unsigned();
                $table->tinyInteger('semester')->unsigned();
                $table->integer('course_id')->unsigned()->index();
                $table->integer('campus_id')->unsigned()->index();
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('course');
                $table->foreign('campus_id')->references('id')->on('campuses');
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
        Schema::drop('course_establish_campus');
        Schema::drop('course_establish_province');
		Schema::drop('course');
	}

}
