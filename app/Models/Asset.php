<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        if($this->status=='OP'){
            return "Operativo";
        }elseif($this->status=='NO'){
            return "No operativo";
        }elseif($this->status=='RE'){
            return "Reparación";
        }else{
            return $this->status;
        }
    }

    public function getStatusLabelAttribute(){
        if($this->status == 'OP'){
            return "<span class='label label-primary' style='font-weight:normal'>Operativo</span>";
        }elseif($this->status == 'NO'){
            return "<span class='label label-danger' style='font-weight:normal'>No operativo</span>";
        }elseif($this->status == 'RE'){
            return "<span class='label label-warning' style='font-weight:normal'>Reparación</span>";
        }else{
            return $this->status;
        }
    }

}
