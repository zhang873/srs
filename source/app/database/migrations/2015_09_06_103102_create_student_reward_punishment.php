<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentRewardPunishment extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('student_reward_punishment') == false) {
            Schema::create('student_reward_punishment', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('student_id')->unsigned()->index();
                $table->integer('reward_level');
                $table->integer('punishment');
                $table->integer('punishment_cause');
                $table->string('remark');
                $table->string('operator');
                $table->tinyInteger('approval_result');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();
            });
        }
        if (Schema::hasTable('reward_info') == false) {
            Schema::create('reward_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->string('reward_level');
                $table->integer('code');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }
        if (Schema::hasTable('punishment_info') == false) {
            Schema::create('punishment_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->string('punishment');
                $table->integer('code');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }
        if (Schema::hasTable('punishment_cause_info') == false) {
            Schema::create('punishment_cause_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->string('punishment_cause');
                $table->integer('code');
                $table->tinyInteger('is_deleted')->default(0);
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
        Schema::drop('student_reward_punishment');
        Schema::drop('reward_info');
        Schema::drop('punishment_info');
        Schema::drop('punishment_cause_info');
    }

}
