<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $table = 'operations';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function company(){
   
        return $this->belongsTo('App\Models\Company');
    }

    public function customer(){
   
        return $this->belongsTo('App\Models\Customer');
    }

    public function comments(){
   
        return $this->hasMany('App\Models\Comment');
    }

    public function partner(){
   
        return $this->belongsTo('App\Models\Partner');
    }

    public function user(){
   
        return $this->belongsTo('App\User');
    }

    //*** Accesors *** 
    public function getStatusDescriptionAttribute(){
        
        switch ($this->status) {
            case '1':
                return "Proceso";
                break;
            case '2':
                return "Pendiente";
                break;
            case '3':
                return "Entregado";
                break;
            
            default:
                return $this->status;
                break;
        }
    }

    public function getStatusLabelAttribute(){
                
        switch ($this->status) {
            case 1:
                return "<span class='label label-default' style='font-weight:normal' title='Cambiar de estado'>$this->status_description</span>";
                break;
            case 2:
                return "<span class='label label-warning' style='font-weight:normal' title='$this->s2_notes'>$this->status_description</span>";
                break;
            case 3:
                return "<span class='label label-primary' style='font-weight:normal' title='$this->s3_notes'>$this->status_description</span>";
                break;
            
            default:
                return $this->status;
                break;
        }

        $label=($this->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }
}
