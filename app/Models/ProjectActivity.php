<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    protected $table = 'project_activities';
    protected $dates = ['date'];    
    
    //*** Relations ***
    public function project(){
   
        return $this->belongsTo('App\Models\Project');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   

}
