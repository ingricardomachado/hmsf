<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $dates = ['planned', 'planned_end', 'started', 'finished'];

    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function activities(){
   
        return $this->hasMany('App\Models\ProjectActivity');
    }

    public function comments(){
   
        return $this->hasMany('App\Models\ProjectComment');
    }

    public function documents(){
   
        return $this->hasMany('App\Models\ProjectDocument');
    }

    public function expenses(){
   
        return $this->hasMany('App\Models\Expense');
    }

    public function fees(){
   
        return $this->hasMany('App\Models\Fee');
    }

    public function incomes(){
   
        return $this->hasMany('App\Models\Income');
    }

    public function photos(){
   
        return $this->hasMany('App\Models\ProjectPhoto');
    }

    public function payments(){
        return Payment::
                join('payment_fee', 'payment_fee.payment_id', '=', 'payments.id')
                ->join('fees', 'payment_fee.fee_id', '=', 'fees.id')
                ->join('properties', 'fees.property_id', '=', 'properties.id')
                ->where('fees.project_id', $this->id)
                ->where('payments.status', 'A')
                ->select('payments.id as id', 'payments.date as date', 'properties.number as property', 'fees.concept as concept', 'payment_fee.amount as amount', 'payments.file as file', 'payments.file_type as file_type');
    }
    
    //*** Accesors ***   
    public function getStatusBtnClassAttribute(){
        
        //P=Planificado, E=Ejecución, F=Finalizada

        if($this->status == 'P'){
            return "btn-warning";
        }else if($this->status == 'E'){
            return "btn-primary";
        }else if($this->status == 'F'){
            return "btn-danger";
        }else if($this->status == 'C'){
            return "btn-danger";
        }else{
            return "btn-default";
        }
    }
    
    public function getStatusDescriptionAttribute(){
        
        if ($this->status == 'P'){        
            return "Planificado";
        }else if($this->status == 'E'){
            return "Ejecución";
        }elseif($this->status == 'F'){
            return "Finalizado";
        }elseif($this->status == 'C'){
            return "Cancelado";
        }else{
            return $this->status;
        }
    }
    
    public function getStatusLabelAttribute(){        
        if($this->status == 'P'){
            return "<span class='label label-warning' style='font-weight:normal'>Planificado</span>";
        }elseif($this->status == 'E'){
            return "<span class='label label-primary' style='font-weight:normal'>Ejecución</span>";
        }elseif($this->status == 'F'){
            return "<span class='label label-danger' style='font-weight:normal'>Finalizado</span>";
        }elseif($this->status == 'C'){
            return "<span class='label label-danger' style='font-weight:normal'>Cancelado</span>";
        }
    }    

}
