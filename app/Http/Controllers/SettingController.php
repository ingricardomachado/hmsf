<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Setting;
use App\Models\Condominium;
use App\Models\Country;
use App\Models\PropertyType;
use App\Models\State;
use Illuminate\Support\Facades\Crypt;
use Session;
//Image
use App\Http\Controllers\ImgController;
use Image;
use File;
use Storage;


class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->condominium = session()->get('condominium');
            return $next($request);
        });    
    }    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function global()
    {
        $setting = Setting::first();        
        return view('settings.global')->with('setting', $setting);
    }

    public function update_global(Request $request)
    {
        $setting = Setting::first();        
        // Codigo para el logo
        $file = Input::file('logo');
        if (File::exists($file))
        {        
            //$img = Image::make($file)->encode('jpg');
            $img = Image::make($file)->encode('jpg');
            //$setting->logo = base64_encode((new ImgController)->resize_image($img, 'jpg', 200, 200));
            $setting->logo = base64_encode($img); 
        }        
        $setting->company= $request->input('company');
        $setting->NIT= $request->input('NIT');
        $setting->address= $request->input('address');
        $setting->phone= $request->input('phone');
        $setting->email= $request->input('email');
        $setting->app_name= $request->input('app_name');
        $setting->coin= $request->input('coin');
        $setting->money_format= $request->input('money_format');
        $setting->save();        
        $this->set_session_global();
        
        return redirect()->route('settings.global')->with('notify', 'update');
    }

    public function condominium()
    {        
        $countries=Country::orderBy('name')->pluck('name','id');
        $states=State::where('country_id', $this->condominium->country_id)->orderBy('name')->pluck('name','id');
        $property_types=PropertyType::orderBy('name')->pluck('name','id');

        
        return view('settings.condominium')->with('condominium', $this->condominium)
                                    ->with('countries', $countries)
                                    ->with('states', $states)
                                    ->with('property_types', $property_types);

    }
    
    public function update_condominium(Request $request)
    {
        $condominium=Condominium::find($this->condominium->id);
        $file = $request->logo;
        if (File::exists($file)){
            Storage::delete($condominium->id.'/'.$condominium->logo);
            Storage::delete($condominium->id.'/thumbs/'.$condominium->logo);
            $condominium->logo_name = $file->getClientOriginalName();
            $condominium->logo_type = $file->getClientOriginalExtension();
            $condominium->logo=$this->upload_file($condominium->id, $file);
        }
        $condominium->name= $request->name;
        $condominium->property_type_id=$request->property_type;
        $condominium->country_id=$request->country;
        $condominium->state_id= $request->state;
        $condominium->city=$request->city;
        $condominium->address=$request->address;
        $condominium->contact=$request->contact;
        $condominium->cell=$request->cell;
        $condominium->phone=$request->phone;
        $condominium->email= $request->email;
        $condominium->save();        
        $this->set_session_condominium($condominium->id);
        
        //return redirect()->route('settings.condominium')->with('notify', 'update');
    }

    public function set_session_global(){
        $setting = Setting::first();
        Session::put('coin', $setting->coin);
        Session::put('money_format', $setting->money_format);
    }

    public function set_session_condominium($id){
        $condominium = Condominium::find($id);
        Session::put('condominium', $condominium);
    }

}
