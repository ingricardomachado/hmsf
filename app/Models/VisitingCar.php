<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitingCar extends Model
{
    protected $table = 'visiting_cars';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }


    //*** Accesors ***   

}
