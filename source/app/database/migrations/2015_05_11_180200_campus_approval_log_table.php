<?php
use Illuminate\Database\Migrations\Migration;

class CampusApprovalLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the users table
        if (Schema::hasTable('campus_approval_log') == false) {
            Schema::create('campus_approval_log', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('action');
                $table->string('campus_id');
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
        Schema::drop('campus_approval_log');
    }
}
