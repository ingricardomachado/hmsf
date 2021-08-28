<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $table = 'centers';
    
    //*** Relations ***
    public function state(){
   
        return $this->belongsTo('App\Models\State');
    }
    
    //*** Accesors ***   
    public function getTypeDescriptionAttribute(){
        
        if($this->type=='R'){
            return "Residencial";
        }elseif($this->type=='C'){
            return "Comercial";
        }else{
            return $this->type;
        }
    }    
    
    public function getStatusLabelAttribute(){
        
        if($this->active){
            return "<span class='label label-primary' style='font-weight:normal'>Activo</span>";
        }else{
            return "<span class='label label-danger' style='font-weight:normal'>Inactivo</span>";
        }
    }
}
