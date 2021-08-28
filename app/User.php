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
                        
        if($this->active){
            return "<span class='label label-primary' style='font-weight:normal'>Activo</span>";
        }else{
            return "<span class='label label-danger' style='font-weight:normal'>Inactivo</span>";
        }
    }

    //*** Mutators****
}
