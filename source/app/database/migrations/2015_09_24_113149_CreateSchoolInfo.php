<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	        if (Schema::hasTable('school_info') == false) {
            Schema::create('school_info', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                
                $table->string('school_name', 255);
                $table->integer('school_id');
                $table->tinyinteger('is_deleted');
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
