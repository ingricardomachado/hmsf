<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\State;
use App\User;
use Session;
use DB;


class ToolController extends Controller
{    
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->center = session()->get('center');
            return $next($request);
        });    
    }

    public function format_ymd($date_dmy)
    {
        $date_ymd = substr($date_dmy, 6, 4).'-'.substr($date_dmy, 3, 2).'-'.substr($date_dmy, 0, 2);
        return $date_ymd;
    }
    
    /**
     * Simple way to generate a random password in PHP.
     *
     * @return \Illuminate\Http\Response
     */
    public function random_password($length)
    {        
        //$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
    
        return $password;
    }

    public function get_states($country_id)
    {
        //if ($request->ajax()){
        $states=State::where('country_id', $country_id)->select('id', 'name')->get();
        return response()->json($states);
        //}
    }

}
