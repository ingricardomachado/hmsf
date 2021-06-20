<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'incomes';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function account(){
   
        return $this->belongsTo('App\Models\Account');
    }

    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function income_type(){
   
        return $this->belongsTo('App\Models\IncomeType');
    }

    public function property(){
   
        return $this->belongsTo('App\Models\Property');
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
