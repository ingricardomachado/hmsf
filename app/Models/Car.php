<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }


    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }

}
