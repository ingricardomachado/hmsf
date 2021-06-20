<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condominium extends Model
{
    protected $table = 'condominiums';
    
    //*** Relations ***
    public function accounts(){
   
        return $this->hasMany('App\Models\Account');
    }

    public function assets(){
   
        return $this->hasMany('App\Models\Asset');
    }

    public function cars(){
   
        return $this->hasMany('App\Models\Car');
    }

    public function contacts(){
   
        return $this->hasMany('App\Models\Contact');
    }

    public function country(){
   
        return $this->belongsTo('App\Models\Country');
    }
    
    public function document_types(){
   
        return $this->hasMany('App\Models\DocumentType');
    }

    public function documents(){
   
        return $this->hasMany('App\Models\Document');
    }

    public function employees(){
   
        return $this->hasMany('App\Models\Employee');
    }

    public function facilities(){
   
        return $this->hasMany('App\Models\Facility');
    }

    public function fees(){
   
        return $this->hasMany('App\Models\Fee');
    }

    public function incomes(){
   
        return $this->hasMany('App\Models\Income');
    }

    public function properties(){
   
        return $this->hasMany('App\Models\Property');
    }

    public function property_type(){
   
        return $this->belongsTo('App\Models\PropertyType');
    }

    public function reservations(){
   
        return $this->hasMany('App\Models\Reservation');
    }
    
    public function state(){
   
        return $this->belongsTo('App\Models\State');
    }

    public function supplier_categories(){
   
        return $this->hasMany('App\Models\SupplierCategory');
    }

    public function suppliers(){
   
        return $this->hasMany('App\Models\Supplier');
    }

    public function users(){
   
        return $this->hasMany('App\User');
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
    
    public function getActiveLabelAttribute(){
        
        if($this->active){
            return "<span class='label label-primary' style='font-weight:normal'>Activo</span>";
        }else{
            return "<span class='label label-danger' style='font-weight:normal'>Inactivo</span>";
        }
    }    

    public function getStatusLabelAttribute(){
        
        $status_lbl = '';
        
        if($this->status == 'A'){
        	$status_lbl = "<span class='label label-primary' style='font-weight:normal'>Activo</span>";
        }elseif($this->status == 'D'){
			$status_lbl = "<span class='label label-danger' style='font-weight:normal'>Inactivo</span>";
        }

        return $status_lbl;
    }    


}
