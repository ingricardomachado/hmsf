<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Operation;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Crypt;

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

            $tot_incomes=0;
            $tot_expenses=0;
            $array_incomes = array_fill(0, $today->month, 0);
            $array_expenses = array_fill(0, $today->month, 0);
            $labels=["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

        if(session('role')=='ADM'){
            
            /*$incomes=$condominium->incomes()
                    ->whereYear('date', $today->year)
                    ->select(
                        DB::raw('DATE_FORMAT(date, "%m") as mes'),
                        DB::raw("sum(amount) as total"))
                    ->groupBy('mes')->get();
            
            foreach ($incomes as $income) {
                $tot_incomes+=$income->total;
                $array_incomes[intval($income->mes-1)]=floatval($income->total);
            }
            
            $expenses=$condominium->expenses()
                    ->whereYear('date', $today->year)
                    ->select(
                        DB::raw('DATE_FORMAT(date, "%m") as mes'),
                        DB::raw("sum(amount) as total"))
                    ->groupBy('mes')->get();
            
            foreach ($expenses as $expense) {
                $tot_expenses+=$expense->total;
                $array_expenses[intval($expense->mes-1)]=floatval($expense->total);
            }*/
            
            return view('home')->with('today', $today)
                        ->with('labels', json_encode($labels))
                        ->with('array_incomes', json_encode($array_incomes))
                        ->with('array_expenses', json_encode($array_expenses));

        }elseif(session('role')=='SOC'){

            return view('home')->with('today', $today)
                        ->with('labels', json_encode($labels))
                        ->with('array_incomes', json_encode($array_incomes))
                        ->with('array_expenses', json_encode($array_expenses));

        }elseif(session('role')=='SUP'){
            
            return view('home')->with('today', $today)
                        ->with('labels', json_encode($labels))
                        ->with('array_incomes', json_encode($array_incomes))
                        ->with('array_expenses', json_encode($array_expenses));

        }elseif(session('role')=='MEN'){
            
            return view('home')->with('today', $today)
                        ->with('labels', json_encode($labels))
                        ->with('array_incomes', json_encode($array_incomes))
                        ->with('array_expenses', json_encode($array_expenses));

        }
    }
}
