<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGraduationInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
public function up()
	{
		// Create the `graduation_info` table
            if (Schema::hasTable('graduation_info') == false)
            {
            Schema::create('graduation_info', function($table)
            {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->tinyinteger('is_applied');
                $table->smallInteger('application_year');
                //$table->tinyInteger('dissertation');
                $table->tinyInteger('application_ semester');
                $table->smallInteger('graduation_year');
                $table->tinyInteger('graduation_semester');
                $table->tinyInteger('approval_status');
                $table->string('elec_regis_no');
                $table->tinyInteger('is_reported');
                $table->tinyInteger('is_deleted');
                
                $table->timestamps();
                
                $table->foreign('student_id')->references('studentno')->on('admissions')->onDelete('cascade');

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
		// Delete the `graduation_info` table
		Schema::drop('graduation_info');
	}
}