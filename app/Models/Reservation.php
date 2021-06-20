<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';
    protected $dates = ['start', 'end'];    
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function facility(){
   
        return $this->belongsTo('App\Models\Facility');
    }

    public function fee(){
   
        return $this->belongsTo('App\Models\Fee');
    }

    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }

    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        /*
            A=Aprobada
            P=Pendiente
            R=Rechazada
        */
        if($this->status=='P'){
            return 'Pendiente';   
        }elseif($this->status=='A'){
            return 'Aprobada';
        }elseif($this->status=='R'){
            return 'Rechazada';
        }else{
            $this->status;
        }
    }

    public function getStatusLabelAttribute(){
        if($this->status=='P'){
            return "<span class='label label-warning' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='A'){
            return "<span class='label label-primary' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='R'){
            return "<span class='label label-danger' style='font-weight:normal'>$this->status_description</span>";
        }else{
            return $this->status;
        }      
    }

    public function getBgColorAttribute(){
        if($this->status=='P'){
            return '#f8ac59'; //warning   
        }elseif($this->status=='A'){
            return '#1ab394'; //primary
        }elseif($this->status=='R'){
            return '#ed5565'; //danger
        }        
    }

}
