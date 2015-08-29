<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('groups') == false) {
            Schema::create('groups', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->string('name', 255);
                $table->string('sysid', 255);
                $table->integer('programs_id');
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
        Schema::drop('groups');
    }

}
