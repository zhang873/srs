<?php
use Illuminate\Database\Migrations\Migration;

class ConfideSetupUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the users table
        if (Schema::hasTable('users') == false) {
            Schema::create('users', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('password');
                $table->string('confirmation_code');
                $table->string('remember_token')->nullable();
                $table->boolean('confirmed')->default(false);
                $table->timestamps();
            });
        }


        // Creates password reminders table
        if (Schema::hasTable('password_reminders') == false) {
            Schema::create('password_reminders', function ($table) {
                $table->engine = 'InnoDB';
                $table->string('email');
                $table->string('token');
                $table->timestamp('created_at');
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
        Schema::drop('password_reminders');
        Schema::drop('users');
    }

}
