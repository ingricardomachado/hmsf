<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment_fee';
    
    //*** Relations ***
    public function payment(){
   
        return $this->belongsTo('App\Models\Payment');
    }

    public function fee(){
   
        return $this->belongsTo('App\Models\Fee');
    }
        
    //*** Accesors ***   
}
