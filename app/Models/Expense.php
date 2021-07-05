<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function account(){
   
        return $this->belongsTo('App\Models\Account');
    }

    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function expense_type(){
   
        return $this->belongsTo('App\Models\ExpenseType');
    }

    public function movement()
    {
        return $this->hasOne('App\Models\Movement');
    }    
    
    public function project(){
   
        return $this->belongsTo('App\Models\Project');
    }

    public function supplier(){
   
        return $this->belongsTo('App\Models\Supplier');
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
