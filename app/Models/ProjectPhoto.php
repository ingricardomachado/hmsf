<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhoto extends Model
{
    protected $table = 'project_photo';
    
    //*** Relations ***
    public function project(){
   
        return $this->belongsTo('App\Models\Project');
    }


    //*** Accesors ***   

}
