<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateTeacherTable extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
        if (Schema::hasTable('Teachers') == false) {
            Schema::create('Teachers', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();

                // general
                $table->string('fullname');
                $table->integer('phone');
                $table->string('chinaid');
                $table->string('gender');
                $table->integer('age');
                $table->string('address');
                $table->integer('postcode');
                $table->string('currschool');
                $table->string('currposition');
                $table->integer('employtype');
                $table->string('degree');
                $table->integer('degreetype');
                $table->integer('teachingage');
                $table->string('graduatedschool');
                $table->string('teaching');
                $table->integer('rank');

                $table->integer('campus_id')->unsigned();
                $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');

                $table->integer('status');
                $table->timestamps();

                // need to save the relationship with "teachers"
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop ( 'Teachers' );
	}
}
