<?php

use Illuminate\Database\Seeder;

class ExpenseTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expense_types')->insert(array(
            'name'      => 'Transferencia entre Cuentas',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'  	=> 'Conserjería',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Seguridad y/o Vigilancia',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Areas Verdes',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Servicio de Agua',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Servicio de Gas',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Energía Eléctrica',
            'active'    => 1
        ));
                
        DB::table('expense_types')->insert(array(
            'name'      => 'Recoleción de Basura',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Mantenimiento General',
            'active'    => 1
        ));
                
        DB::table('expense_types')->insert(array(
            'name'      => 'Mantenimiento de Instalación',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Reparación General',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Reparación de Instalación',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Material de Oficina',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Material de Limpieza',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Honorario',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Sueldo y/o Salario',
            'active'    => 1
        ));

        
        DB::table('expense_types')->insert(array(
            'name'      => 'Gasto Administrativo',
            'active'    => 1
        ));
        
        DB::table('expense_types')->insert(array(
            'name'      => 'Gasto Operativo',
            'active'    => 1
        ));

        DB::table('expense_types')->insert(array(
            'name'      => 'Otros Egresos',
            'active'    => 1
        ));
    }
}
