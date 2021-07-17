<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fee extends Model
{
    protected $table = 'fees';
    protected $dates = ['date', 'due_date'];

    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function income_type(){
   
        return $this->belongsTo('App\Models\IncomeType');
    }

    public function payments(){
   
        return $this->belongsToMany('App\Models\Payment','payment_fee')
                            ->withPivot('amount')
                            ->withTimestamps();
    }
    
    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }

    public function reservation(){
   
        return $this->belongsTo('App\Models\Event');
    }
    
    //*** Methods ***
    public function update_balance(){
        $this->balance=$this->amount-$this->payments()
                                        ->where('payments.status', 'A')
                                        ->sum('payment_fee.amount');
        $this->save();        
    }

    //*** Accesors ***   
    public function getRemainigDaysAttribute(){
        
        $now = Carbon::now();
        return $this->due_date->diffInDays($now, false);
    }

    public function getStatusLabelAttribute(){
                
        if($this->balance<=0){
            return "<span class='label label-primary' style='font-weight:normal'>Solvente</span>";
        }elseif($this->balance>0 && $this->remainig_days<=0){
            return "<span class='label label-warning' style='font-weight:normal'>Pendiente</span>";
        }elseif($this->balance>0 && $this->remainig_days>0){
            return "<span class='label label-danger' style='font-weight:normal'>Morosa</span>";
        }
    }    

    public function getStatusDescriptionAttribute(){
                
        if($this->balance<=0){
            return "Solvente";
        }elseif($this->balance>0 && $this->remainig_days<=0){
            return "Pendiente";
        }elseif($this->balance>0 && $this->remainig_days>0){
            return "Morosa";
        }
    }    

    public function getBulletAttribute(){
                
        if($this->balance<=0){
            return "<i class='fa fa-circle text-navy' aria-hidden='true'></i>";
        }elseif($this->balance>0 && $this->remainig_days<=0){
            return "<i class='fa fa-circle text-warning' aria-hidden='true'></i>";
        }elseif($this->balance>0 && $this->remainig_days>0){
            return "<i class='fa fa-circle text-danger' aria-hidden='true'></i>";
        }
    }    


    public function getRemainigDaysDescriptionAttribute(){
        
        if($this->balance>0 && $this->remainig_days==0){
            return "Vence hoy";
        }elseif($this->balance>0 && $this->remainig_days==-1){
            return 'Resta 1 día';
        }elseif($this->balance>0 && $this->remainig_days<0){
            return 'Restan '.abs($this->remainig_days).' días';
        }elseif($this->balance>0 && $this->remainig_days==1){
            return "1 día vencida";
        }elseif($this->balance>0 && $this->remainig_days>1){
            return $this->remainig_days.' días vencida';
        }else{
            return "";
        }
    }

}
