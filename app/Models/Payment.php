<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    
    //*** Relations ***
    public function account(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }


    //*** Accesors ***   

}
