<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //教学计划
        if (Schema::hasTable('program_info') == false) {
	        Schema::create('program_info', function($table)
	        {
	            $table->engine = 'InnoDB';
	            $table->increments ('id');
	            $table->string ('code', 7);
	            $table->smallInteger ('year')->unsigned();
	            $table->tinyInteger('semester')->unsigned();
	            $table->tinyInteger ('student_classification');
	            $table->tinyInteger ('major_classification');
	            $table->string ('major');
	            $table->float ('min_credit_graduation');
	            $table->float ('schooling_period');
	            $table->float ('max_credit_exemption');
	            $table->float ('max_credit_semester');
	            $table->tinyInteger ('is_activated')->default(1);
	            $table->tinyInteger ('is_deleted')->default(0);
	            $table->timestamps();
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
        Schema::drop('program_info');
	}

}
