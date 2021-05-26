<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $table = 'expense_types';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function expenses(){
   
        return $this->hasMany('App\Models\Expense');
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
