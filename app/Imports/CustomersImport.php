<?php

namespace App\Imports;

use App\User;
use App\Models\Partner;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $socio=trim($row[0]);
            $cliente=trim($row[1]);
            if(!$this->customer_exist($cliente)){
                $customer = new Customer();
                $customer->number=Customer::max('number')+1;
                $customer->partner_id=$this->get_partner_id($socio);
                $customer->name=$cliente;
                $customer->tax=floatval($row[2])*100;
                $customer->save();
            }
        }
        echo "Fin de importacion de clientes";
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

    private function customer_exist($cliente)
    {
        return Customer::where('name', $cliente)->exists();
    }
}