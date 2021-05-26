<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';
    protected $dates = ['start', 'end'];

    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function reservations(){
   
        return $this->hasMany('App\Models\Reservation');
    }
    
    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        if($this->status=='O'){
            return 'Operativo';
        }elseif($this->status=='R'){
            return 'Reparación';
        }elseif($this->status=='M'){
            return 'Mantenimiento';
        }elseif($this->status=='N'){
            return 'No operativo';
        }else{
            return $this->status;
        }
    }

    public function getStatusLabelAttribute(){
        if($this->status=='O'){
            return "<span class='label label-primary' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='R'){
            return "<span class='label label-warning' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='M'){
            return "<span class='label label-warning' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='N'){
            return "<span class='label label-danger' style='font-weight:normal'>$this->status_description</span>";
        }else{
            return $this->status;
        }      
    }

}
