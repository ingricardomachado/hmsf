<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MovementsExport implements FromView 
{
	protected $start;
    protected $account;
    protected $movements;    

	function __construct($start, $account, $movements) {
        
        $this->start = $start;
        $this->account = $account;
        $this->movements = $movements;
 	}

	public function view(): View
    {   
        return view('reports.xls_movements', [
            'start' => $this->start,
            'account' => $this->account,
            'movements' => $this->movements,
        ]);
    }
}
