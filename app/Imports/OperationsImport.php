<?php

namespace App\Imports;

use App\User;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OperationsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            //echo $row['comision_hm'].'<br>';
            $fecha=trim(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha'])->format('d/m/Y'));
            $socio=trim($row['socio']);
            $cliente=trim($row['cliente']);
            $empresa=trim($row['empresa']);
            $folio=trim($row['folio']);
            $facturado=floatval(trim($row['facturado']));
            $comision_cli=floatval(trim($row['comision_cli']))*100;
            $comision_sc=floatval(trim($row['comision_sc']))*100;
            $comision_hm=100-$comision_sc;

            $operation = new Operation();
            $operation->number=Operation::max('number')+1;
            $operation->customer_id=$this->get_customer_id($cliente);
            $operation->partner_id=$this->get_partner_id($socio);
            $operation->date=Carbon::createFromFormat('d/m/Y', $fecha);
            $operation->company_id=$this->get_company_id($empresa);
            $operation->folio=$folio;
            $operation->amount=$facturado;
            $operation->customer_tax=$comision_cli;
            $operation->partner_tax=$comision_sc;
            $operation->hm_tax=$comision_hm;
            $operation->customer_profit=$operation->amount*($operation->customer_tax/100);
            $operation->partner_profit=$operation->customer_profit*($operation->partner_tax/100);
            $operation->hm_profit=$operation->customer_profit*($operation->hm_tax/100);
            $operation->return_amount=$operation->amount-$operation->customer_profit;
            $operation->status=3; //todas como finalizadas
            $operation->save();
        }
        echo "Fin de importacion de operaciones";
    }
    
    private function get_customer_id($cliente)
    {
        if(Customer::where('name', $cliente)->exists()){
            $customer=Customer::where('name', $cliente)->first();
            return $customer->id;
        }else{
            return null;
        }
    }

    private function get_partner_id($socio)
    {
        if(User::where('full_name', $socio)->where('role', 'SOC')->exists()){
            $user=User::where('full_name', $socio)->where('role', 'SOC')->first();
            $partner=Partner::where('user_id', $user->id)->first();
            return $partner->id;
        }else{
            return null;
        }
    }
    
    private function get_company_id($empresa)
    {
        if(Company::where('name', $empresa)->exists()){
            $company=Company::where('name', $empresa)->first();
            return $company->id;
        }else{
            return null;
        }
    }

    public function headingRow(): int
    {
        return 1; //el header esta es la linea 1
    }
}