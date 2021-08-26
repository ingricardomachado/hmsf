<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(array(        
            'company'      => 'HM Soluciones Financieras',
            'NIT'      => 'J-9999999',
            'address'      => 'Sector Tipuro. Monagas, VEN',
            'phone'      => ' +58 5439974',
            'email'      => 'hmsoluciones@prueba.com',
            'app_name'  	=> 'HM Soluciones v1.0',
        	'coin' => '$',
        	'money_format' => 'PC2',
            'tax' => 10,
        	'created_at' => '2019-01-01',
        	'updated_at' => '2019-01-01',            
 		));    
    }
}
