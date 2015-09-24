<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardPunishment extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 if (Schema::hasTable('reward_info') == false) {
            Schema::create('reward_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->string('reward_level');
                $table->Integer('code');
                $table->tinyInteger('is_deleted');
                $table->timestamps();

            });
        }

	        if (Schema::hasTable('punishment_info') == false) {
            Schema::create('punishment_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->string('punishment');
                $table->Integer('code');
                $table->tinyInteger('is_deleted');
                $table->timestamps();

            });
        }
        
        if (Schema::hasTable('punishment_cause_info') == false) {
        	Schema::create('punishment_cause_info', function ($table) {
        		$table->engine = 'InnoDB';
        		$table->increments('id')->unsigned()->index();
        		$table->string('punishment_cause');
        		$table->Integer('code');
        		$table->tinyInteger('is_deleted');
        		$table->timestamps();
        
        	});
        }
        
        if (Schema::hasTable('student_reward_punishment') == false) {
        	Schema::create('student_reward_punishment', function ($table) {
        		$table->engine = 'InnoDB';
        		$table->increments('id')->unsigned()->index();
        		$table->datetime('date');
        		$table->string('remark');
        		$table->string('document_id');
        		$table->string('operator');
        		$table->tinyInteger('approval_result');
        		$table->tinyInteger('is_deleted');
        		$table->timestamps();
        
        		$table->Integer('reward_level');
        		$table->Integer('punishment');
        		$table->Integer('punishment_cause');
        		$table->string('student_id',15);
        		//$table->foreign('reward_level')->references('code')->on('reward_info');
        		//$table->foreign('punishment')->references('code')->on('punishment_info');
        		//$table->foreign('punishment_cause')->references('code')->on('punishment_cause_info');

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
		Schema::drop ( 'reward_info' );
		Schema::drop ( 'punishment_info' );
		Schema::drop ( 'punishment_cause_info' );
		Schema::drop ( 'student_reward_punishment' );
	}

}
