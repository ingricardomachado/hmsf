<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfers';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function from_account(){
   
        return $this->belongsTo('App\Models\Account', 'from_account_id');
    }

    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function to_account(){
   
        return $this->belongsTo('App\Models\Account', 'to_account_id');
    }

    public function movements()
    {
        return $this->hasMany('App\Models\Movement');
    }    
        

    //*** Accesors ***   
    public function getPaymentMethodDescriptionAttribute(){
        
        if($this->payment_method=='EF'){
            return "Efectivo";
        }elseif($this->payment_method=='TA'){
            return "Transferencia";
        }elseif($this->payment_method=='CH'){
            return "Cheque";
        }elseif($this->payment_method=='OT'){
            return "Otro";
        }else{
            $this->payment_method;
        }
    }

}
