<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{

    protected $table = 'partners';
   
   //*** Relations ***    
    public function customers()
    {        
        return $this->belongsTo('App\Models\Customer');
    }

    public function user()
    {        
        return $this->belongsTo('App\User');
    }
        
    public function state()
    {        
        return $this->belongsTo('App\Models\State');
    }

    //*** Accesors ***
    public function getStatusDescriptionAttribute(){
        
        return ($this->user->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->user->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }
}
