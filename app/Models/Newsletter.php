<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletters';
    protected $dates = ['date'];    
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   
    public function getLevelDescriptionAttribute(){
        if($this->level==1){
            return "Alta";
        }elseif($this->level==2){
            return "Media";
        }elseif($this->level==3){
            return "Baja";
        }else{
            return $this->level;
        }
    }

    public function getLevelLabelAttribute(){
        if($this->level==1){
            return "<span class='label label-danger' style='font-weight:normal'>Alta</span>";
        }elseif($this->level==2){
            return "<span class='label label-warning' style='font-weight:normal'>Media</span>";
        }elseif($this->level==3){
            return "<span class='label label-gray' style='font-weight:normal'>Baja</span>";
        }else{
            return $this->level;
        }
    }

}
