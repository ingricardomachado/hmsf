<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table = 'movements';
    protected $guarded = [];
    protected $dates = ['date'];    
    
    //*** Relations ***    
    public function account(){
   
        return $this->belongsTo('App\Models\Account');
    }

    public function expense(){
   
        return $this->belongsTo('App\Models\Expense');
    }

    public function income(){
   
        return $this->belongsTo('App\Models\Income');
    }

    public function payment(){
   
        return $this->belongsTo('App\Models\Payment');
    }

    public function transfer(){
   
        return $this->belongsTo('App\Models\Transfer');
    }

    //*** Accesors ***   
    public function getTypeDescriptionAttribute(){
        return ($this->type=='D')?'Débito':'Crédito';        
    }

    public function getReferenceAttribute(){
        if($this->income_id){
            return $this->income->reference;
        }elseif($this->expense_id){
            return $this->expense->reference;
        }elseif($this->payment_id){
            return $this->payment->reference;
        }elseif($this->transfer_id){
            return $this->transfer->reference;
        }        
    }

    public function getConceptAttribute(){
        if($this->income_id){
            return $this->income->concept;
        }elseif($this->expense_id){
            return (($this->expense->supplier_id)?$this->expense->supplier->name.' - ':'').$this->expense->concept;
        }elseif($this->payment_id){
            return $this->payment->property->number.' - '.$this->payment->concept;
        }elseif($this->transfer_id){
            return $this->transfer->concept;
        }        
    }

}
