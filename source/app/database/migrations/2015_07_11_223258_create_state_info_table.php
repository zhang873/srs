<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //状态表
        if (Schema::hasTable('state_info') == false) {
            Schema::create('state_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('campus_id')->unique()->unsigned()->index();
                $table->tinyInteger('campus_selection');
                $table->tinyInteger('campus_confirmation');
                $table->tinyInteger('province_student_selection');
                $table->tinyInteger('campus_student_selection');
                $table->tinyInteger('student_status_changing');
                $table->timestamps();

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
        Schema::drop('state_info');
	}

}
