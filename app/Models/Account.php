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
