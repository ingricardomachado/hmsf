<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PropertiesExport implements FromView 
{
	protected $condominium;

	function __construct($condominium) {
        $this->condominium = $condominium;
 	}

	public function view(): View
    {   
        return view('reports.xls_properties', [
            'properties' => $this->condominium->properties()->get()
        ]);
    }
}
