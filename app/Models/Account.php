<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $dates = ['date_initial_balance'];
    
    //*** Relations ***
    public function condominium(){
   
        return $this->belongsTo('App\Models\Condomnium');
    }

    public function expenses(){
   
        return $this->hasMany('App\Models\Expense');
    }

    public function incomes(){
   
        return $this->hasMany('App\Models\Income');
    }

    public function movements(){
   
        return $this->hasMany('App\Models\Movement');
    }

    public function payments(){
   
        return $this->hasMany('App\Models\Payment');
    }

    //*** Methods ***
    public function update_balance(){
        //credits
        $this->credits=$this->movements()->where('type','C')->sum('amount');
        //debits
        $this->debits=$this->movements()->where('type','D')->sum('amount');
        
        $this->balance=$this->initial_balance+$this->credits-$this->debits;
        $this->save();
    }

    public function balance_at($date){
        //initial balance verificado antes o depues de la fecha
        $date->subDays(1);

        $initial_balance=($this->date_initial_balance->diffInDays($date, false)>=0)?$this->initial_balance:0;
        
        //credits a la fecha
        $credits=$this->payments()
                                ->whereDate('date','>=',$date)
                                ->whereDate('date','<',$date)
                                ->where('status','A')->sum('amount')+
                        $this->incomes()
                                ->whereDate('date','>=',$date)
                                ->whereDate('date','<',$date)->sum('amount');
        //debits a la fecha
        $debits=$this->expenses()
                                ->whereDate('date','>=',$date)
                                ->whereDate('date','<',$date)->sum('amount');
        
        return $initial_balance+$credits-$debits;
    }    

    //*** Accesors ***   
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getTypeDescriptionAttribute(){
        if($this->type=='C'){
            return "Caja";
        }elseif($this->type=='B'){
            return "Banco";
        }else{
            return $this->type;
        }        
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='label label-".$label."' style='font-weight:normal'>$this->status_description</span>";       
    }

}
