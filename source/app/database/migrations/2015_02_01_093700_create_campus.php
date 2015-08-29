<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateCampus extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Create the `admissions` table

        if (Schema::hasTable('campuses') == false) {

            Schema::create('campuses', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned()->index();

                // general
                $table->string('name')->nullable();
                $table->integer('userID')->unsigned();
                $table->integer('address1');
                $table->string('address2')->nullable();
                $table->string('applicationorg')->nullable();
                $table->string('representatives')->nullable();
                $table->integer('nature');
                $table->integer('supportorg');
                $table->string('contactperson')->nullable();
                $table->string('contactmobile')->nullable();
                $table->string('contactphone')->nullable();
                $table->integer('admincampus');
                $table->string('postalcode')->nullable();
                $table->string('description')->nullable();
                $table->string('manager')->nullable();
                $table->date('date');

                // equipment
                $table->integer('kusatellite');
                $table->integer('viaccsatellite');
                $table->integer('ipsatellite');
                $table->integer('remotesatellite');
                $table->integer('backbone');
                $table->integer('bbtopc');
                $table->integer('boardband');
                $table->integer('conntype');
                $table->integer('bandwidth');
                $table->integer('surveillance');
                $table->integer('totalnetpc');
                $table->integer('networkrm');
                $table->integer('networkrmpc');
                $table->integer('networkrmvideo');
                $table->integer('mediarm');
                $table->integer('mediarmpc');
                $table->integer('mediarmpjr');
                $table->integer('mediarmpjrvideo');
                $table->integer('mediarmpjraudio');
                $table->integer('mediarmpjrrgb');
                $table->integer('mediarmtv');
                $table->integer('mediarmtvvideo');
                $table->integer('mediarmtvaudio');
                $table->integer('mediarmtvrgb');
                $table->integer('Visualiser');
                $table->integer('teacherpanel');
                $table->integer('videodisplay');
                $table->integer('generalrm');
                $table->integer('generalrmwtv');
                $table->integer('generalrmttltv');
                $table->integer('generalrmouctv');
                $table->integer('audiobigrm');
                $table->integer('audiorm');
                $table->integer('library');
                $table->integer('librarytxt');
                $table->integer('libraryref');
                $table->integer('libraryother1');
                $table->integer('librarycd');
                $table->integer('librarytape');
                $table->integer('libraryother2');
                $table->integer('fulltime');
                $table->integer('fulltimewdegree');
                $table->integer('ta1');
                $table->integer('partime');
                $table->integer('fulltime2');
                $table->integer('ta2');
                $table->integer('admin');
                $table->integer('tech');
                $table->integer('other');
                $table->integer('intpilotsite');
                $table->string('intpilotsitename')->nullable();
                $table->integer('extpilotsite');
                $table->string('extpilotsitename')->nullable();
                $table->integer('pilotsitetools');
                $table->integer('pilotsiteteacher');
                $table->string('approval_comment')->nullable();

                $table->integer('status');
                $table->timestamps();

                // need to save the relationship with "user"
                $table->foreign('userID')->references('id')->on('users');
            });
        }
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		// Delete the `campuses` table
		Schema::drop ( 'campuses' );
	}
}