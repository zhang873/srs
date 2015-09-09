<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionForm extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
public function up()
	{
		// Create the `admissions` table
            if (Schema::hasTable('admissions') == false)
            {
            Schema::create('admissions', function($table)
            {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->string('fullname');
                $table->string('gender');
                $table->integer('politicalstatus');
                $table->integer('idtype');
                $table->string('idnumber');
                $table->date('dateofbirth');
                $table->string('nationgroup');
                $table->integer('occupation');
                $table->integer('maritalstatus');
                $table->integer('hukou');
                $table->string('jiguan')->nullable();
                $table->string('hometown')->nullable();
                $table->string('mobile');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->integer('postcode')->nullable();
                $table->integer('program');
                $table->integer('programcode')->unsigned();
                $table->integer('campuscode')->unsigned();
                $table->integer('status');
                $table->string('admissionid', 100);
                $table->string('ksh', 17)->nullable();
                $table->smallInteger('enrollmenttype');
                $table->string('studentno', 15);
                $table->smallInteger('admissionyear');
                $table->string('admissionsemester', 3);
                
         
                $table->tinyInteger('distribution')->nullable();//new
                $table->tinyInteger('is_serving')->nullable();//new
                $table->string('company_organization')->nullable();//new
                $table->string('company_address')->nullable();//new
                $table->integer('company_postcode')->nullable()->nullable();//new
                $table->string('company_phone')-->nullable();//new
                $table->string('email')->nullable();//new
                $table->tinyInteger('is_deleted')->nullable();//new

                $table->tinyInteger();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('programcode')->references('id')->on('programs')->onDelete('cascade');
                $table->foreign('campuscode')->references('id')->on('campuses')->onDelete('cascade');
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
		// Delete the `admissions` table
		Schema::drop('admissions');
	}
}