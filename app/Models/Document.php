<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function document_type(){
   
        return $this->belongsTo('App\Models\DocumentType');
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
