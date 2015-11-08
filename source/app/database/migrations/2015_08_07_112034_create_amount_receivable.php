<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmountReceivable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mod_fin_amount_receivable', function(Blueprint $table)
		{
			// create amount_receviable table's column
            $table->increments('id')->unsigned()->index();
            $table->integer('transactionid');
            $table->integer('transactiontype');
            $table->double('amount', 10, 1)->default('0.0');
            $table->integer('parent')->default('0');
            $table->boolean('printed')->default(false);
            $table->integer('printtimes')->default('0');
            $table->boolean('locked')->default(false);
            $table->boolean('deleted')->default(false);
            $table->string('sysid', 12)->default('0');
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
		Schema::table('mod_fin_amount_receivable', function(Blueprint $table)
		{
			// delete the table
			Schema::drop('mod_fin_amount_receivable');
		});
	}

}
