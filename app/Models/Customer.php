<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    
    //*** Relations ***
    public function partner(){
   
        return $this->belongsTo('App\Models\Partner');
    }

    //*** Accesors ***   
    public function getCodeAttribute(){
        
        return "HM".substr($this->partner->user->first_name, 0, 1).substr($this->partner->user->last_name, 0, 1).'-'.str_pad($this->number, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }
}
