<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'visits';
    protected $dates = ['checkin', 'checkout'];
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }

    public function visiting_car(){
   
        return $this->belongsTo('App\Models\VisitingCar');
    }

    public function visit_type(){
   
        return $this->belongsTo('App\Models\VisitType');
    }
    
    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }
    
    public function user(){
   
        return $this->belongsTo('App\User');
    }

    public function visitor(){
   
        return $this->belongsTo('App\Models\Visitor');
    }

    //*** Accesors ***   
    public function getDownloadFileAttribute(){
        if($this->file){                    
            $ext=$this->file_type;
            if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                $url_show_file = url('visit_image', $this->id);
                return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$this->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
            }else{
                $url_download_file = route('visits.download', $this->id);
                return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$this->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
            }
        }else{
            return "";
        }
    }
}
