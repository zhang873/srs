<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawprograms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// 'raw program' table
        if (Schema::hasTable('rawprograms') == false) {
            Schema::create('rawprograms', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->string('name');
                $table->string('type');
                $table->string('idnumber');
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
		// drop 'raw program' table
	    Schema::drop ( 'rawprograms' );
	}

}
