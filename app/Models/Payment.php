<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function account(){
   
        return $this->belongsTo('App\Models\Account');
    }

    public function condominium(){
   
        return $this->belongsTo('App\Models\Condominium');
    }
    
    public function fees(){
   
        return $this->belongsToMany('App\Models\Fee','payment_fee')
                            ->withPivot('amount')
                            ->withTimestamps();
    }
    
    public function movement()
    {
        return $this->hasOne('App\Models\Movement');
    }    

    public function property(){
   
        return $this->belongsTo('App\Models\Property');
    }

    //*** Accesors ***   
    public function getPaymentMethodDescriptionAttribute(){
        
        if($this->payment_method=='EF'){
            return "Efectivo";
        }elseif($this->payment_method=='TA'){
            return "Transferencia";
        }elseif($this->payment_method=='CH'){
            return "Cheque";
        }elseif($this->payment_method=='OT'){
            return "Otro";
        }else{
            $this->payment_method;
        }
    }
    
    public function getStatusDescriptionAttribute(){
        /*
            A=Aprobado
            P=Por confirmar
            R=Rechazado
        */
        if($this->status=='P'){
            return 'Por confirmar';   
        }elseif($this->status=='A'){
            return 'Aprobado';
        }elseif($this->status=='R'){
            return 'Rechazado';
        }else{
            $this->status;
        }
    }

    public function getStatusLabelAttribute(){
        if($this->status=='P'){
            return "<span class='label label-warning' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='A'){
            return "<span class='label label-primary' style='font-weight:normal'>$this->status_description</span>";
        }elseif($this->status=='R'){
            return "<span class='label label-danger' style='font-weight:normal'>$this->status_description</span>";
        }else{
            return $this->status;
        }      
    }

    public function getBgColorAttribute(){
        if($this->status=='P'){
            return '#f8ac59'; //warning   
        }elseif($this->status=='A'){
            return '#1ab394'; //primary
        }elseif($this->status=='R'){
            return '#ed5565'; //danger
        }        
    }

    public function getDownloadFileAttribute(){
        if($this->file){                    
            $ext=$this->file_type;
            if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                $url_show_file = url('payment_image', $this->id);
                return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$this->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
            }else{
                $url_download_file = route('payments.download', $this->id);
                return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$this->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
            }
        }else{
            return "";
        }
    }
}
