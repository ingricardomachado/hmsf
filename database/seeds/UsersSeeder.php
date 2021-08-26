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
            'first_name'  => 'Administrador',
            'last_name'  => 'Demo',
            'full_name'  => 'Administrador Demo',
            'password'  => Hash::make('123456'),
            'email'  	=> 'admin@hmsoluciones.com',
            'role'     => 'ADM',
            'active'     => 1,
            'created_at' => '2020-01-01 00:00:00',
            'updated_at' => '2020-01-01 00:00:00',
        ));
    }
}
