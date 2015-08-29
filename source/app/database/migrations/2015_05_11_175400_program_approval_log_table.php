<?php
use Illuminate\Database\Migrations\Migration;

class ProgramApprovalLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the users table
        if (Schema::hasTable('program_approval_log') == false) {
            Schema::create('program_approval_log', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('action');
                $table->string('program_id');
                $table->string('comments')->nullable();
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
        Schema::drop('program_approval_log');
    }
}
