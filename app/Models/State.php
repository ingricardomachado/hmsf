<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    
    //*** Relations ***
    public function condominiums(){
   
        return $this->hasMany('App\Models\Condominium');
    }

    public function country(){
   
        return $this->belongsTo('App\Models\Country');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    

}
