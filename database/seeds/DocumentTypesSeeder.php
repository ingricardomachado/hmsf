<?php

use Illuminate\Database\Seeder;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('document_types')->insert(array(
            'name'  	=> 'Carta',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Memorándum',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Oficio',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Circular',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Manual',
            'active'      => 1
        ));
    
        DB::table('document_types')->insert(array(
            'name'      => 'Guía o Instructivo',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Norma o Reglamento',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Procedimiento',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Reporte',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Bitácora',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Informe',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Minuta',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Aviso o Anuncio',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Acta',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Convocatoria',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Constancia',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Autorización',
            'active'      => 1
        ));
        DB::table('document_types')->insert(array(
            'name'      => 'Recibo',
            'active'      => 1
        ));
        
        DB::table('document_types')->insert(array(
            'name'      => 'Factura',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Presupuesto',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Nota de Entrega',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'EMail',
            'active'      => 1
        ));

        DB::table('document_types')->insert(array(
            'name'      => 'Catálogo',
            'active'      => 1
        ));
    }
}
