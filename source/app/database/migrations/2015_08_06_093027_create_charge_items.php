<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_charge_items', function(Blueprint $table)
		{
			// create transaction_definition table
		    $table->increments('id')->unsigned()->index();;
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
		Schema::table('mod_fin_charge_items', function(Blueprint $table)
		{
			// drop the table transaction_definition
			Schema::drop('mod_fin_charge_items');
		});
	}

}
