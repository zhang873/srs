<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateAdmissionDetails extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Create the `admission_details` table
        if (Schema::hasTable('admission_details') == false) {
            Schema::create('admission_details', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->integer('admission_id')->unsigned();
                $table->integer('ougraduated');
                $table->integer('formerlevel');
                $table->string('formerschool')->nullable();
                $table->date('dategraduated')->nullable();
                $table->string('attainmentcert')->nullable();
                $table->integer('attainment');
                $table->integer('attainmentcat');
                $table->integer('attainmenttype');
                $table->string('attainmentcno')->nullable();
                $table->integer('attainmentproof');
                $table->string('attainmentpno')->nullable();
                $table->string('originalname')->nullable();
                $table->string('originalid')->nullable();
                $table->string('examresult')->nullable();
                $table->string('photo')->nullable();
                $table->string('attachment')->nullable();

                $table->integer('status');
                $table->timestamps();
                $table->foreign('admission_id')->references('id')->on('admissions')->onDelete('cascade');
            });
        }
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		// Delete the `admissions` table
		Schema::drop ( 'admission_details' );
	}
}