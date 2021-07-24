<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Movement;
use DB;
use Session;
use Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
        
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        $today=Carbon::now();
        $current_year=Carbon::now()->format('Y');

        
        $array_total_credit_points = [0,0,0,0,0,0];
        $array_total_debit_points = [0,0,0,0,0,0];
        
        $labels=($today->month <= 6)?["Ene", "Feb", "Mar", "Abr", "May", "Jun"]:["Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        
        if(Session::get('role')=='SAM'){
            return redirect()->route('condominiums.index');
        }else{
            return view('home')->with('today', $today)
                        ->with('tot_customers', 0)
                        ->with('tot_purchases', 0)
                        ->with('tot_credit_points', 0)
                        ->with('tot_debit_points', 0)
                        ->with('top_customers', 0)
                        ->with('tot_credit_points_year', 0)
                        ->with('tot_debit_points_year', 0)
                        ->with('labels', json_encode($labels))
                        ->with('array_total_credit_points', json_encode($array_total_credit_points))
                        ->with('array_total_debit_points', json_encode($array_total_debit_points));

        }
    }
}
