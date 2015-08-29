<?php

class RolesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('roles')->delete();

        $adminRole = new Role;
        $adminRole->name = 'admin';
        $adminRole->save();
        
        $staffRole = new Role;
        $staffRole->name = 'staff';
        $staffRole->save();

        $user = User::where('username','=','admin')->first();
        $user->attachRole( $adminRole );

        $user = User::where('username','=','staff')->first();
        $user->attachRole( $staffRole );
    }

}
