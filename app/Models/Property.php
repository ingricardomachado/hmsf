<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'properties';
    
    //*** Relations ***
    public function cars(){
   
        return $this->hasMany('App\Models\Car');
    }
    
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function reservations(){
   
        return $this->hasMany('App\Models\Reservation');
    }
    
    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   
    public function getStatusLabelAttribute(){
        
        $status_lbl = '';
        if($this->status=='S'){
            $status_lbl = "<span class='label label-primary' style='font-weight:normal'>Solvente</span>";
        }elseif($this->status=='P'){
            $status_lbl = "<span class='label label-warning' style='font-weight:normal'>Pendiente</span>";
        }elseif($this->status=='M'){
            $status_lbl = "<span class='label label-danger' style='font-weight:normal'>Moroso</span>";
        }else{
        	return $this->status;
        }

        return $status_lbl;
    }    

}
