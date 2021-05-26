<?php

use Illuminate\Database\Seeder;

class PropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_types')->insert(array(
            'name'  	=> 'Casa',
            'active'      => 1
        ));

        DB::table('property_types')->insert(array(
            'name'      => 'Apartamento',
            'active'      => 1
        ));

        DB::table('property_types')->insert(array(
            'name'      => 'Townhouse',
            'active'      => 1
        ));

        DB::table('property_types')->insert(array(
            'name'      => 'Local Comercial',
            'active'      => 1
        ));

        DB::table('property_types')->insert(array(
            'name'      => 'Terreno',
            'active'      => 1
        ));
    
        DB::table('property_types')->insert(array(
            'name'      => 'Consultorio Médico',
            'active'      => 1
        ));

        DB::table('property_types')->insert(array(
            'name'      => 'Otro',
            'active'      => 1
        ));
    }
}
