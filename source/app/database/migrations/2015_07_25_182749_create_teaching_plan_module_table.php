<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachingPlanModuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        if (Schema::hasTable('teaching_plan_module') == false) {
            Schema::create('teaching_plan_module', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('teaching_plan_id')->unsigned()->index();
                $table->integer('module_id')->unsigned()->index();
                $table->integer('credit');
                $table->integer('min_credit');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('teaching_plan_id')->references('id')->on('teaching_plan');
                $table->foreign('module_id')->references('id')->on('major_module_info');

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
        Schema::drop('teaching_plan_module');
	}

}
