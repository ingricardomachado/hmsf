<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $dates = ['date'];
    
    //*** Relations ***
    public function center(){
   
        return $this->belongsTo('App\Models\Center');
    }

    public function expense_type(){
   
        return $this->belongsTo('App\Models\ExpenseType');
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

    public function getDownloadFileAttribute(){
        if($this->file){                    
            $ext=$this->file_type;
            if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                $url_show_file = url('expense_image', $this->id);
                return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$this->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
            }else{
                $url_download_file = route('expenses.download', $this->id);
                return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$this->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
            }
        }else{
            return "";
        }
    }

}
