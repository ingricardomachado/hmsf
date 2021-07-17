<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    protected $table = 'project_comment';
    
    //*** Relations ***
    public function project(){
   
        return $this->belongsTo('App\Models\Project');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   

}
