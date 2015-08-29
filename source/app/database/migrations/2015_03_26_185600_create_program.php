<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateProgram extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Create the `admissions` table
        if (Schema::hasTable('programs') == false) {
            Schema::create('programs', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();

                // general
                $table->string('name');
                $table->integer('rank')->nullable();
                $table->integer('status');
                $table->integer('campus_id')->unsigned();
                $table->string('approval_comment')->nullable();
                $table->timestamps();

                // need to save the relationship with "campus"
                $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('program_teacher') == false) {
            Schema::create('program_teacher', function ($table) {
                $table->increments('id');
                $table->integer('program_id')->unsigned()->index();
                $table->integer('teacher_id')->unsigned()->index();
                $table->unique(array('program_id', 'teacher_id'));
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
                $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            });
        }
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		// Delete the `programs` table
		Schema::drop ( 'program_teacher' );
		Schema::drop ( 'programs' );
	}
}