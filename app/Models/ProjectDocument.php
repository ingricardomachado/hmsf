<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    protected $table = 'project_document';
    
    //*** Relations ***
    public function project(){
   
        return $this->belongsTo('App\Models\Project');
    }


    //*** Accesors ***   

}
