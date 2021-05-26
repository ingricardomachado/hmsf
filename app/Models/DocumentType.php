<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function documents(){
   
        return $this->hasMany('App\Models\Document');
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
