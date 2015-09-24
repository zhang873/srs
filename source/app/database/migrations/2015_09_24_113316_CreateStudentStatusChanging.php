<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentStatusChanging extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	
        if (Schema::hasTable('student_status_changing') == false) 
        {
            Schema::create('student_status_changing', function ($table) 
            {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->smallInteger('application_year');
                $table->tinyInteger('application_semester');
                $table->string('cause',255);
                $table->tinyInteger('approval_status');
                $table->string('remark',255);
                $table->tinyInteger('is_deleted');
                
                $table->string('original_major_id');
                $table->string('current_major_id');
                $table->integer('original_campus_id')->unsigned();
                $table->integer('current_campus_id')->unsigned();
                $table->string('original_class_id');
                $table->string('current_class_id');              
                $table->string('student_id',15);
                
                //$table->foreign('original_major_id')->references('idnumber')->on('rawprograms');
                //$table->foreign('current_major_id')->references('idnumber')->on('rawprograms');
                //$table->foreign('original_campus_id')->references('id')->on('campuses');
                //$table->foreign('current_campus_id')->references('id')->on('campuses');
                //$table->foreign('original_class_id')->references('sysid')->on('groups');
                //$table->foreign('current_class_id')->references('sysid')->on('groups');
                //$table->foreign('student_id')->references('studentno')->on('admissions');
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
		Schema::drop('student_status_changing');
	}

}
