<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateExpelDropoutRecovery extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

	        if (Schema::hasTable('student_expel') == false) {
            Schema::create('student_expel', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                
                $table->smallint('expel_year');
                $table->tinyinteger('expel_semester');
                $table->string('cause');
                $table->string('document_id');
                $table->string('remark');              
                $table->tinyint('is_deleted');
                $table->timestamps();
                
                $table->foreign('student_id')->references('studentno')->on('admissions');
                
            });
        }
        
        if (Schema::hasTable('student_dropout') == false) {
        	Schema::create('student_dropout', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                
                $table->smallint('application_year');
                $table->tinyinteger('application_semester');
                $table->string('cause');
                $table->tinyinteger('approval_result_ province');
                $table->string('approval_suggestion_province');              
                $table->tinyint('is_deleted');
                $table->timestamps();

        		$table->foreign('student_id')->references('studentno')->on('admissions');

        	});
        }
        
        if (Schema::hasTable('student_recovery') == false) {
        	Schema::create('student_recovery', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();

                $table->smallint('recovery_year');
                $table->tinyinteger('recovery_semester');
                $table->tinyinteger('student_status');
                $table->tinyinteger('approval_result');   
                $table->string('remark');
                $table->tinyint('is_deleted');
                $table->timestamps();
                
                $table->foreign('student_id')->references('studentno')->on('admissions');
                
        	});
        }

	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		// Delete the all the tables
		Schema::drop ( 'student_expel');
		Schema::drop ( 'student_dropout');
		Schema::drop ( 'student_recovery');
	}
}