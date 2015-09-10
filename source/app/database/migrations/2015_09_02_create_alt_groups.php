<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('groups') == false) {
            Schema::create('groups', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();
                $table->string('name', 255);
                $table->string('sysid', 255);
                $table->integer('programs_id');
                $table->timestamps();
                
                $table->tinyInteger('major_classification')->nullable();//new
                $table->string('major',255)->nullable();//new
                $table->smallInteger('year')->nullable();//new
                $table->tinyInteger('semester')->nullable();//new
                $table->tinyInteger('having_stu_no')->nullable();//new
                $table->string('class_adviser',255)->nullable();//new
                $table->string('exam_point',255)->nullable();//new
                $table->tinyInteger('is_deleted')->nullable();//new
                
                $table->unsigned('campus_id');//new
                $table->integer('school_id');//new
                
                $table->foreign('campus_id')->references('id')->on('campuses');
                $table->foreign('school_id')->references('school_id')->on('school_info');
                 
                
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
        Schema::drop('groups');
    }

}
