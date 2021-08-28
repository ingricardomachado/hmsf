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
    public function index()
    {
        $setting = Setting::first();        
        return view('settings.index')->with('setting', $setting);
    }

    public function update(Request $request)
    {
        try {
    
            $setting = Setting::first();        
            $file = $request->logo;
            if (File::exists($file)){
                Storage::delete('settings/'.$setting->logo);
                Storage::delete('settings/thumbs/'.$setting->logo);
                $setting->logo_name = $file->getClientOriginalName();
                $setting->logo_type = $file->getClientOriginalExtension();
                $setting->logo_size = $file->getSize();
                $setting->logo=$this->upload_file('settings', $file);
            }
            $setting->company= $request->company;
            $setting->NIT= $request->NIT;
            $setting->address= $request->address;
            $setting->phone= $request->phone;
            $setting->cell= $request->cell;
            $setting->email= $request->email;
            $setting->coin= $request->coin;
            $setting->money_format= $request->money_format;
            $setting->tax= $request->tax;
            $setting->save();        
            $this->set_session($setting);
            
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

    public function set_session($setting){
        Session::put('coin', $setting->coin);
        Session::put('money_format', $setting->money_format);
    }

}
