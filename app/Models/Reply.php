<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';
    
    //*** Relations ***
    public function post(){
   
        return $this->belongsTo('App\Models\Post');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Methods ***

    //*** Accesors ***   

}
