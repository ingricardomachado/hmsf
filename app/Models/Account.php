<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $dates = ['date_initial_balance'];
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function incomes(){
   
        return $this->hasMany('App\Models\Income');
    }

    public function payments(){
   
        return $this->hasMany('App\Models\Payment');
    }

    //*** Methods ***
    public function update_credits(){
        //Pagos a cuotas o ingresos extraordinarios
        $this->credits= $this->payments()->sum('amount')+
                        $this->incomes()->sum('amount');
        
        $this->balance=$this->initial_balance+$this->credits-$this->debits;
        $this->save();
    }    

    public function update_debits(){
        $this->debits=0;
        
        $this->balance=$this->initial_balance+$this->credits-$this->debits;
        $this->save();
    }    


    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getTypeDescriptionAttribute(){
        if($this->type=='C'){
            return "Caja";
        }elseif($this->type=='B'){
            return "Banco";
        }else{
            return $this->type;
        }        
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }

}
