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
        try {
    
            $setting = Setting::first();        
            $file = $request->logo;
            if (File::exists($file)){
                Storage::delete('global/'.$setting->logo);
                Storage::delete('global/thumbs/'.$setting->logo);
                $setting->logo_name = $file->getClientOriginalName();
                $setting->logo_type = $file->getClientOriginalExtension();
                $setting->logo_size = $file->getSize();
                $setting->logo=$this->upload_file('global', $file);
            }
            $setting->company= $request->input('company');
            $setting->NIT= $request->input('NIT');
            $setting->address= $request->input('address');
            $setting->phone= $request->input('phone');
            $setting->email= $request->input('email');
            $setting->app_name= $request->input('app_name');
            $setting->save();        
            $this->set_session_global();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones actualizadas exitosamente'
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
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
        try {
            $condominium=Condominium::find($this->condominium->id);
            $file = $request->logo;
            if (File::exists($file)){
                Storage::delete($condominium->id.'/'.$condominium->logo);
                Storage::delete($condominium->id.'/thumbs/'.$condominium->logo);
                $condominium->logo_name = $file->getClientOriginalName();
                $condominium->logo_type = $file->getClientOriginalExtension();
                $condominium->logo_size = $file->getSize();
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
            $condominium->coin= $request->input('coin');
            $condominium->money_format= $request->input('money_format');
            $condominium->save();        
            $this->set_session_condominium($condominium->id);        
            
            return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones actualizadas exitosamente'
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    public function set_session_global(){
        $setting = Setting::first();
    }

    public function set_session_condominium($id){
        $condominium = Condominium::find($id);
        Session::put('condominium', $condominium);
        Session::put('coin', $condominium->coin);
        Session::put('money_format', $condominium->money_format);
    }

}
