<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    
    //*** Relations ***
    public function condominiums(){
   
        return $this->hasMany('App\Models\Condomnium');
    }

    public function states(){
   
        return $this->hasMany('App\Models\States');
    }

    //*** Methods ***
    
    //*** Accesors ***    

}
