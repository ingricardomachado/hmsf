<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //*** Relations ***    

    //*** Method ***    
    
    //*** Accesors ***
    public function getRoleDescriptionAttribute(){
        
        if($this->role == 'ADM'){
            return "Administrador";
        }else if($this->role == 'SOC'){
            return "Socio de negocio";
        }else if($this->role == 'SUP'){
            return "Supervisor";
        }else if($this->role == 'MEN'){
            return "Mensajero";
        }else{
            return $this->role;
        }
    }

    public function getStatusDescriptionAttribute(){
        
        if ($this->active){        
            return "Activo";
        }else{
            return "Inactivo";
        }
    }

    public function getStatusLabelAttribute(){
                
        $status_lbl = '';
        
        if($this->active){
            $status_lbl = "<span class='label label-primary' style='font-weight:normal'>Activo</span>";
        }else{
            $status_lbl = "<span class='label label-danger' style='font-weight:normal'>Inactivo</span>";
        }

        return $status_lbl;
    }

    public function getPropertiesLabelAttribute(){
        
        $lbl_start="<span class='form-group label label-default' style='padding-top:1mm;padding-bottom:1mm;font-weight:normal'>";
        $lbl_end="</span>";
        $properties_lbl = '';
        $properties = $this->properties()->orderBy('number')->get();

        foreach ($properties as $property) {
            ($properties_lbl=='')?$properties_lbl=$lbl_start.$property->number.$lbl_end:$properties_lbl=$properties_lbl.' '.$lbl_start.$property->number.$lbl_end;
        }
        return $properties_lbl;
    }    

}
