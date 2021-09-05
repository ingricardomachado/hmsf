<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    
    //*** Relations ***
    public function operation(){
   
        return $this->belongsTo('App\Models\Operation');
    }
    
    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors ***   
}
