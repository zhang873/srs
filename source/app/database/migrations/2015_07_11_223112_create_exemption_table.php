<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExemptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */

    public function up()
    {

        if (Schema::hasTable('exemption_major_outer') == false) {
            Schema::create('exemption_major_outer', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('major_name');
                $table->string('code', 5);
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        if (Schema::hasTable('exemption_agency') == false) {
            Schema::create('exemption_agency', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('agency_name');
                $table->string('code', 5);
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        if (Schema::hasTable('exemption_type') == false) {
            Schema::create('exemption_type', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('exemption_type_name');
                $table->string('code', 5);
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

            });
        }

        //免修免考信息表
        if (Schema::hasTable('exemption_info') == false) {
            Schema::create('exemption_info', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('student_id')->unsigned()->index();;
                $table->integer('course_id')->unsigned()->index();;
                $table->string('major_name_outer');
                $table->string('course_name_outer');
                $table->tinyinteger('credit_outer');
                $table->tinyinteger('classification_outer');
                $table->string('score');
                $table->smallinteger('application_year')->unsigned();
                $table->tinyinteger('application_semester')->unsigned();
                $table->integer('exemption_type_id')->unsigned()->index();
                $table->integer('certification_year')->unsigned();
                $table->integer('agency_id')->unsigned()->index();;
                $table->tinyinteger('final_result');
                $table->longText('remark')->nullable();
                $table->longText('failure_cause');
                $table->tinyInteger('is_deleted')->default(0);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('course');
                $table->foreign('student_id')->references('id')->on('admissions');
                $table->foreign('exemption_type_id')->references('id')->on('exemption_type');
                $table->foreign('agency_id')->references('id')->on('exemption_agency');

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
        Schema::drop('exemption_type');
        Schema::drop('exemption_agency');
        Schema::drop('exemption_major_outer');
        Schema::drop('exemption_info');
    }

}
