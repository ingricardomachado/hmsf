<?php

use Illuminate\Database\Seeder;

class IncomeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('income_types')->insert(array(
            'name'      => 'Pago de Cuota',
            'active'    => 1
        ));
        
        DB::table('income_types')->insert(array(
            'name'      => 'Transferencia entre Cuentas',
            'active'    => 1
        ));
        
        DB::table('income_types')->insert(array(
            'name'      => 'Uso de instalación',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Multas o penalización',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'  	=> 'Cuota Ordinaria',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Cuota Extraordinaria',
            'active'    => 1
        ));

                        
        DB::table('income_types')->insert(array(
            'name'      => 'Saldo inicial',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Interés Bancario',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Alquiler',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Donación',
            'active'    => 1
        ));

        DB::table('income_types')->insert(array(
            'name'      => 'Consumo de servicio',
            'active'    => 1
        ));
        
        DB::table('income_types')->insert(array(
            'name'      => 'Otra fuente de ingreso',
            'active'    => 1
        ));
    }
}
