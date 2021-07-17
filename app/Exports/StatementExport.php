<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatementExport implements FromView 
{
    protected $property;
    protected $fees;    

	function __construct($property, $fees) {
        
        $this->property = $property;
        $this->fees = $fees;
 	}

	public function view(): View
    {   
        return view('reports.xls_statement', [
            'property' => $this->property,
            'fees' => $this->fees,
        ]);
    }
}
