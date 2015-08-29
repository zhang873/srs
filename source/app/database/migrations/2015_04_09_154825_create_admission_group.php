<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('admission_group') == false) {
            Schema::create('admission_group', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->integer('admission_id');
                $table->integer('group_id');
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
        Schema::drop('admission_group');
    }

}
