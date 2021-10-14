<?php

namespace App\Imports;

use App\User;
use App\Models\Company;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CompaniesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $empresa=trim($row[0]);
            if(!$this->company_exist($empresa)){
                $company = new Company();
                $company->name=$empresa;
                $company->save();
            }
        }
        echo "Fin de importacion de empresas";
    }

    private function company_exist($empresa)
    {
        return Company::where('name', $empresa)->exists();
    }
}