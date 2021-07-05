<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'properties';
    
    //*** Relations ***
    public function cars(){
   
        return $this->hasMany('App\Models\Car');
    }
    
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function fees(){
   
        return $this->hasMany('App\Models\Fee');
    }

    public function payments(){
   
        return $this->hasMany('App\Models\Payment');
    }
    
    public function reservations(){
   
        return $this->hasMany('App\Models\Reservation');
    }
    
    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   
    public function getDebtAttribute(){
        $today=Carbon::now();

        return $this->fees()
                        ->where('balance','>',0)
                        ->whereDate('due_date','>=',$today)
                        ->sum('balance');
    }    

    public function getDueDebtAttribute(){
        $today=Carbon::now();

        return $this->fees()
                        ->where('balance','>',0)
                        ->whereDate('due_date','<',$today)
                        ->sum('balance');
    }    

    public function getTotalDebtAttribute(){
        return $this->debt+$this->due_debt;
    }    

    public function getStatusLabelAttribute(){
        
        if($this->total_debt==0){
            return "<span class='label label-primary' style='font-weight:normal'>Solvente</span>";
        }else{
            if($this->due_debt>0){
                return "<span class='label label-danger' style='font-weight:normal'>Moroso</span>";
            }else{
                return "<span class='label label-warning' style='font-weight:normal'>Pendiente</span>";
            }
        }
    }    

}
