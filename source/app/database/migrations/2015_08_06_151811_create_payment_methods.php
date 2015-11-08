<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentMethods extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_payment_methods', function(Blueprint $table)
		{
			// create table mod_fin_payment_methods
		    $table->increments('id')->unsigned()->index();
		    $table->string('name');
		    $table->string('sysid')->default('0');
		    $table->boolean('deleted')->default(false);
		    $table->timestamp('created_at');
		    $table->timestamp('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mod_fin_payment_methods', function(Blueprint $table)
		{
			// drop the table
		    Schema::drop('mod_fin_payment_methods');
		});
	}

}
