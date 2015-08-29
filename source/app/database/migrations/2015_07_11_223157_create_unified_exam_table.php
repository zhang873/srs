<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnifiedExamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        if (Schema::hasTable('unified_exam_subject') == false) {
            Schema::create('unified_exam_subject', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('subject');
                $table->string('code');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        if (Schema::hasTable('unified_exam_cause') == false) {
            Schema::create('unified_exam_cause', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('cause');
                $table->string('code');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        if (Schema::hasTable('unified_exam_type') == false) {
            Schema::create('unified_exam_type', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('type');
                $table->string('code');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        if (Schema::hasTable('unified_exam_info') == false) {
            Schema::create('unified_exam_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('student_id')->unsigned()->index();;
                $table->smallinteger('registration_year');
                $table->tinyinteger('registration_semester');
                $table->integer('unified_exam_subject_id')->unsigned()->index();;
                $table->integer('unified_exam_cause_id')->unsigned()->index();;
                $table->integer('unified_exam_type_id')->unsigned()->index();;
                $table->longText('failure_cause');
                $table->tinyinteger('final_result');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('admissions');
                $table->foreign('unified_exam_subject_id')->references('id')->on('unified_exam_subject');
                $table->foreign('unified_exam_cause_id')->references('id')->on('unified_exam_cause');
                $table->foreign('unified_exam_type_id')->references('id')->on('unified_exam_type');

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
        Schema::drop('unified_exam_type');
        Schema::drop('unified_exam_cause');
        Schema::drop('unified_exam_subject');
        Schema::drop('unified_exam_info');
	}

}
