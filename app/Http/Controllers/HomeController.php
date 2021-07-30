<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Movement;
use App\Models\Condominium;
use App\Models\Property;
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

        if(session('role')=='SAM'){
            return redirect()->route('condominiums.index');
        }elseif(session('role')=='ADM'){

            $condominium=Condominium::find(session('condominium')->id);
            
            $tot_incomes=0;
            $tot_expenses=0;
            $array_incomes = array_fill(0, $today->month, 0);
            $array_expenses = array_fill(0, $today->month, 0);
            $labels=["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
            
            $incomes=$condominium->incomes()
                    ->whereYear('date', $today->year)
                    ->select(
                        DB::raw('DATE_FORMAT(date, "%m") as mes'),
                        DB::raw("sum(amount) as total"))
                    ->groupBy('mes')->get();
            
            foreach ($incomes as $income) {
                $tot_incomes+=$income->total;
                $array_incomes[intval($income->mes-1)]=floatval($income->total);
            }

            $payments=$condominium->payments()
                    ->whereYear('date', $today->year)
                    ->select(
                        DB::raw('DATE_FORMAT(date, "%m") as mes'),
                        DB::raw("sum(amount) as total"))
                    ->groupBy('mes')->get();

            foreach ($payments as $income) {
                $tot_incomes+=$income->total;
                $array_incomes[intval($income->mes-1)]+=floatval($income->total);
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
            }
            
            $debt=$condominium->fees()->whereDate('due_date','>=',$today)->sum('balance');
            $due_debt=$condominium->fees()->whereDate('due_date','<',$today)->sum('balance');
            $tot_debt=$condominium->fees()->sum('balance');

            $solventes=0;
            $pendientes=0;
            $morosos=0;
            $properties=$condominium->properties()->get();

            foreach ($properties as $property) {
                if($property->status==0){
                    $solventes++;
                }elseif($property->status==1){
                    $pendientes++;
                }elseif($property->status==2){
                    $morosos++;
                }
            }

            $accounts=$condominium->accounts()->get();
            $events=$condominium->events()
                          ->whereDate('start', '>=', $today)
                          ->whereDate('start', '<=', $today->addDays(2))
                          ->get();

            $pending_payments=$condominium->payments()->where('status', 'P')->get();

            return view('home')->with('today', $today)
                        ->with('accounts', $accounts)
                        ->with('pending_payments', $pending_payments)
                        ->with('events', $events)
                        ->with('solventes', $solventes)
                        ->with('pendientes', $pendientes)
                        ->with('morosos', $morosos)
                        ->with('debt', $debt)
                        ->with('due_debt', $due_debt)
                        ->with('tot_debt', $tot_debt)
                        ->with('labels', json_encode($labels))
                        ->with('array_incomes', json_encode($array_incomes))
                        ->with('array_expenses', json_encode($array_expenses));

        }elseif(session('role')=='OWN'){
            return view('home_own')->with('today', $today)
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
        }elseif(session('role')=='WAM'){
            return redirect()->route('newsletters.index');
        }
    }
}
