<?php

namespace App\Imports;

use App\User;
use App\Models\Partner;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartnersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            //echo $row[0].' '.$row[1].'<br>';
            $socio= explode(" ", trim($row[0]));
            if(!$this->partner_exist($row[0])){
                $user = new User();
                $user->first_name=$socio[0];
                $user->last_name=$socio[1];
                $user->full_name=$user->first_name.' '.$user->last_name;
                $user->email=strtolower($user->first_name).'.'.strtolower($user->last_name).'@hmsolucionesfinancieras.com';
                $user->role='SOC';
                $user->password=bcrypt('123456');
                $user->save();                
                $partner = new Partner();
                $partner->number=Partner::max('number')+1;                
                $partner->user_id=$user->id;
                $partner->state_id=15;
                $partner->tax=floatval($row[1])*100;
                $partner->save();
            }
        }
        echo "Fin de importacion de socios";
    }

    private function partner_exist($socio)
    {
        return User::where('full_name', $socio)->where('role','SOC')->exists();
    }
}