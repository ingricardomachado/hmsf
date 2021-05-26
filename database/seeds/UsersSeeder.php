<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(array(
            'name'  => 'Super Administrador',
            'password'  => Hash::make('123456'),
            'email'  	=> 'superadmin@smartcond.com',
            'role'     => 'SAM',
            'active'     => 1,
            'created_at' => '2020-01-01 00:00:00',
            'updated_at' => '2020-01-01 00:00:00',
        ));
    }
}
