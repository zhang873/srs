<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleCurrentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //各模块当前年度学期
        if (Schema::hasTable('module_current') == false) {
            Schema::create('module_current', function ($table) {
                $table->engine = 'InnoDB';
                $table->integer('module_id')->unsigned()->unique();
                $table->smallInteger('current_year')->unsigned();
                $table->tinyInteger('current_semester')->unsigned();
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
		Schema::drop('module_current');
	}

}
