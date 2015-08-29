<?php
class UsersTableSeeder extends Seeder {
	public function run() {
		DB::table ( 'users' )->delete ();
		
		$users = array (
				array (
						'username' => 'admin',
						'email' => 'admin@example.org',
						'password' => Hash::make ( 'admin' ),
						'confirmed' => 1,
						'confirmation_code' => md5 ( microtime () . Config::get ( 'app.key' ) ),
						'created_at' => new DateTime (),
						'updated_at' => new DateTime () 
				),
				array (
						'username' => 'staff',
						'email' => 'staff@example.org',
						'password' => Hash::make ( 'staff' ),
						'confirmed' => 1,
						'confirmation_code' => md5 ( microtime () . Config::get ( 'app.key' ) ),
						'created_at' => new DateTime (),
						'updated_at' => new DateTime () 
				) 
		);
		
		DB::table ( 'users' )->insert ( $users );
	}
}
