<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Models\Condominium;
use App\Models\Setting;
use App\Jobs\SendWelcomeEmails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        //Registra el condominio
        $condominium=new Condominium();
        $condominium->type=$data['type'];
        $condominium->property_type_id=$data['property_type'];
        $condominium->name=$data['condominium'];
        $condominium->country_id=$data['country'];
        $condominium->state_id=$data['state'];
        $condominium->max_properties=$data['properties'];
        $condominium->contact=$data['contact'];
        $condominium->cell=$data['cell'];
        $condominium->email=$data['email'];
        $condominium->save();
        //Registra el usuario administrador
        $user=new User();
        $user->condominium_id=$condominium->id;
        $user->name=$data['contact'];
        $user->email=$data['email'];
        $user->cell=$data['cell'];
        $user->role='ADM';
        $user->active=1;
        $user->password=bcrypt($data['password']);
        $user->save();
        //Setear variables de sesion
        $setting = Setting::first();
        Session::put('role', $user->role);
        Session::put('company_name', $setting->company);
        Session::put('condominium', $condominium);
        Session::put('coin', $condominium->coin);
        Session::put('money_format', $condominium->money_format);
        //Enviar correos de notificacion al administrador y a SmartCond
        SendWelcomeEmails::dispatch($user);
        //Mail::to('ing.ricardo.machado@gmail.com')->send(new SignedupCondominium($condominium));
        //Mail::to($user->email)->send(new WelcomeAdmin($user));
        return $user;
    }
}
