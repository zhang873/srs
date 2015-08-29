<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreatePlan extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Create the `admissions` table
        if (Schema::hasTable('Plans') == false) {
            Schema::create('Plans', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();

                // general
                $table->integer('program_id')->unsigned();
                $table->integer('proposedquota')->nullable();
                $table->integer('finalquota')->nullable();
                $table->integer('status');
                $table->string('approval_comment')->nullable();
                // admission year
                $table->smallInteger('admissionyear');

                /// admission semester
                $table->string('admissionsemester', 3);
                $table->timestamps();

                // need to save the relationship with "program"
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');

            });
        }
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		// Delete the `Plans` table
		Schema::drop ( 'Plans' );
	}
}