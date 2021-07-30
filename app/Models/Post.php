<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    public function replies(){
   
        return $this->hasMany('App\Models\Reply');
    }

    //*** Methods ***

    //*** Accesors ***   

}
