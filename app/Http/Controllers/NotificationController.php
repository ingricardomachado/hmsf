<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CarRequest;
use App\Models\Car;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use App\Jobs\SendFreeEmail;

class NotificationController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->condominium=session()->get('condominium');
            return $next($request);
        });    

    }    
    
    /**
     * Display a listing of the car.
     *
     * @return \Illuminate\Http\Response
     */
    public function email()
    {                
        $properties=$this->condominium->properties()
                    ->join('users', 'properties.user_id', '=', 'users.id')
                    ->select('properties.id', 'properties.number', 'users.name as user', 'users.email as email')->get();
        
        return view('notifications.email')->with('properties', $properties);
    }

    /**
     * Display a listing of the car.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_email(Request $request)
    {                
        
        try {
                        
            $subject=$request->subject;
            $body=$request->body;
            $array_properties=$request->array_properties;
            for ($i=0; $i < count($array_properties); $i++) {
                $property=Property::find($array_properties[$i]);                
                SendFreeEmail::dispatch($property, $subject, $body);
            }

            return response()->json([
                    'success' => true,
                    'message' => 'Correos enviados exitosamente'
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

}
