<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Operation;
use App\Models\Expense;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Crypt;

class HomeController extends Controller
{

    protected $labels_days=["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"];

    protected $labels_months=["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

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
        $tot_incomes_day=0;
        $tot_incomes_month=0;
        $tot_expenses_month=0;
            
        if(session('role')=='ADM'){
            
            if(Operation::count()>0){
                $last_operation=Operation::orderBy('date','DESC')->first();
                $last_day=$last_operation->date;                
            }else{
                $last_day=Carbon::now();
            }
            
            $tot_incomes_month=Operation::
                            whereYear('date', '=', $last_day->year)
                            ->whereMonth('date', '=', $last_day->month)->sum('amount');
            $tot_expenses_month=Expense::
                            whereYear('date', '=', $last_day->year)
                            ->whereMonth('date', '=', $last_day->month)->sum('amount');
            
            $tot_customers=Customer::all()->count();
            $tot_operations=Operation::all()->count();
                                    
            return view('home')
                        ->with('last_day', $last_day)
                        ->with('tot_customers', $tot_customers)
                        ->with('tot_operations', $tot_operations)
                        ->with('tot_incomes_month', $tot_incomes_month)
                        ->with('tot_expenses_month', $tot_expenses_month);

        }elseif(session('role')=='SOC'){
            
            $partner=Partner::where('user_id', Auth::user()->id)->first();

            if(Operation::where('partner_id', $partner->id)->count()>0){
                $last_operation=Operation::where('partner_id', $partner->id)->orderBy('date','DESC')->first();
                $last_day=$last_operation->date;                
            }else{
                $last_day=Carbon::now();
            }
            
            $tot_incomes_month=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $last_day->year)
                            ->whereMonth('date', '=', $last_day->month)->sum('amount');
            
            $tot_customers=Customer::where('partner_id', $partner->id)->count();
            $tot_operations=Operation::where('partner_id', $partner->id)->count();            
            
            return view('home_sc')
                        ->with('last_day', $last_day)
                        ->with('tot_customers', $tot_customers)
                        ->with('tot_operations', $tot_operations)
                        ->with('tot_incomes_month', $tot_incomes_month)
                        ->with('tot_incomes_year', 0)                        
                        ->with('tot_margin_sc_month', 0)
                        ->with('tot_margin_sc_year', 0);

        
        }elseif(session('role')=='SUP'){
            
            return redirect()->route('operations.index');

        }elseif(session('role')=='MEN'){
            
            return redirect()->route('operations.index');
        }
    }

    public function load_graph_om($year, $month){
                        
        $years=Operation::
                    select(DB::raw('YEAR(date) year'))
                    ->groupBy('year')
                    ->pluck('year', 'year');
                
        $tot_incomes_month=0;

         if(session('role')=='ADM'){
                        
            $incomes_month=Operation::
                            whereYear('date', '=', $year)
                            ->whereMonth('date', '=', $month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('dia')->get();

        $max_day=($incomes_month->count()>0)?$incomes_month->max('dia'):1;

         }elseif(session('role')=='SOC'){
            
            $partner=Partner::where('user_id', Auth::user()->id)->first();
                                    
            $incomes_month=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $year)
                            ->whereMonth('date', '=', $month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('dia')->get();

            $max_day=($incomes_month->count()>0)?$incomes_month->max('dia'):0;
         }
        
        $array_incomes_month = array_fill(0, $max_day, 0);

        foreach ($incomes_month as $income) {
            $tot_incomes_month+=$income->amount;
            ($income->dia==$max_day)?$tot_incomes_day=$income->amount:'';
            $array_incomes_month[intval($income->dia-1)]=round(floatval($income->amount), 2);
        }
    
        return view('graph_om')
                ->with('years', $years)
                ->with('today', Carbon::now())
                ->with('year', $year)
                ->with('month', $month)
                ->with('tot_incomes_month', $tot_incomes_month)
                ->with('array_incomes_month', json_encode($array_incomes_month))
                ->with('labels_days', json_encode($this->labels_days));
    }

    public function load_graph_mm($year, $month){
        
        $years=Operation::
                    select(DB::raw('YEAR(date) year'))
                    ->groupBy('year')
                    ->pluck('year', 'year');
        
        $today=Carbon::now();
        
        $tot_margin_total_month=0;
        $tot_margin_sc_month=0;
        $tot_margin_hm_month=0;

        $tot_incomes_month=0;

         if(session('role')=='ADM'){
            
            $margins_month=Operation::
                            whereYear('date', '=', $year)
                            ->whereMonth('date', '=', $month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('dia')->get();
            
            $max_day=($margins_month->count()>0)?$margins_month->max('dia'):1;
            
            $array_margin_total_month = array_fill(0, $max_day, 0);
            $array_margin_sc_month = array_fill(0, $max_day, 0);
            $array_margin_hm_month = array_fill(0, $max_day, 0);                
            $array_incomes_month = array_fill(0, $max_day, 0);
            
            foreach ($margins_month as $margin) {
                $array_margin_total_month[intval($margin->dia-1)]=round(floatval($margin->customer_profit), 2);
                $array_margin_sc_month[intval($margin->dia-1)]=round(floatval($margin->partner_profit), 2);
                $array_margin_hm_month[intval($margin->dia-1)]=round(floatval($margin->hm_profit), 2);
            }

            $tot_margin_total_month=Operation::
                                whereYear('date', '=', $year)
                                ->whereMonth('date', '=', $month)
                                ->sum('customer_profit');
         
            $tot_margin_sc_month=Operation::
                                whereYear('date', '=', $year)
                                ->whereMonth('date', '=', $month)
                                ->sum('partner_profit');

            $tot_margin_hm_month=Operation::
                                whereYear('date', '=', $year)
                                ->whereMonth('date', '=', $month)
                                ->sum('hm_profit');

         }elseif(session('role')=='SOC'){
            
            $partner=Partner::where('user_id', Auth::user()->id)->first();
            
            $margins_month=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $year)
                            ->whereMonth('date', '=', $month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('dia')->get();
            
            foreach ($margins_month as $margin) {
                $array_margin_sc_month[intval($margin->dia-1)]=round(floatval($margin->partner_profit), 2);
            }
         
            $tot_margin_sc_month=Operation::
                                where('partner_id', $partner->id)
                                ->whereYear('date', '=', $year)
                                ->whereMonth('date', '=', $month)
                                ->sum('partner_profit');
         }
            
        if(session('role')=='ADM'){
            
            return view('graph_mm_adm')
                ->with('today', $today)
                ->with('years', $years)
                ->with('year', $year)
                ->with('month', $month)
                ->with('tot_margin_total_month', $tot_margin_total_month)
                ->with('tot_margin_sc_month', $tot_margin_sc_month)
                ->with('tot_margin_hm_month', $tot_margin_hm_month)
                ->with('labels_days', json_encode($this->labels_days))
                ->with('array_margin_total_month', json_encode($array_margin_total_month))
                ->with('array_margin_sc_month', json_encode($array_margin_sc_month))
                ->with('array_margin_hm_month', json_encode($array_margin_hm_month));

        }elseif(session('role')=='SOC'){
            
            return view('graph_mm_sc')
                ->with('today', $today)
                ->with('years', $years)
                ->with('year', $year)
                ->with('month', $month)
                ->with('tot_margin_sc_month', $tot_margin_sc_month)
                ->with('labels_days', json_encode($this->labels_days))
                ->with('array_margin_sc_month', json_encode($array_margin_sc_month));
        }
    }

    public function load_graph_oy($year){
                        
        $years=Operation::
                    select(DB::raw('YEAR(date) year'))
                    ->groupBy('year')
                    ->pluck('year', 'year');
        
        $tot_incomes_year=0;

        if(session('role')=='ADM'){
        
            $incomes_year=Operation::
                            whereYear('date', '=', $year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('mes')->get();
        
            $max_month=($incomes_year->count()>0)?$incomes_year->max('mes'):1;

            $array_incomes_year = array_fill(0, $max_month, 0);
        
        }elseif(session('role')=='SOC'){
            
            $partner=Partner::where('user_id', Auth::user()->id)->first();

            $incomes_year=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('mes')->get();
            
            $max_month=($incomes_year->count()>0)?$incomes_year->max('mes'):1;

            $array_incomes_year = array_fill(0, $max_month, 0);
        }
        
        foreach ($incomes_year as $income) {
            $tot_incomes_year+=$income->amount;
            $array_incomes_year[intval($income->mes-1)]=round(floatval($income->amount), 2);
        }
        
        return view('graph_oy')
                    ->with('today', Carbon::now())
                    ->with('years', $years)
                    ->with('year', $year)
                    ->with('labels_months', json_encode($this->labels_months))
                    ->with('tot_incomes_year', $tot_incomes_year)
                    ->with('array_incomes_year', json_encode($array_incomes_year));
    }

    public function load_graph_my($year){
                        
        $years=Operation::
                    select(DB::raw('YEAR(date) year'))
                    ->groupBy('year')
                    ->pluck('year', 'year');
        
        $tot_margin_total_year=0;
        $tot_margin_sc_year=0;
        $tot_margin_hm_year=0;        

        if(session('role')=='ADM'){
            
            $margins_year=Operation::
                            whereYear('date', '=', $year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('mes')->get();
            
            $max_month=($margins_year->count()>0)?$margins_year->max('mes'):1;

            $array_margin_total_year = array_fill(0, $max_month, 0);
            $array_margin_sc_year = array_fill(0, $max_month, 0);
            $array_margin_hm_year = array_fill(0, $max_month, 0);
            
            foreach ($margins_year as $margin) {
                $tot_margin_total_year+=$margin->customer_profit;
                $tot_margin_sc_year+=$margin->partner_profit;
                $tot_margin_hm_year+=$margin->hm_profit;

                if($margin->mes==$max_month){
                    $tot_margin_total_month=$margin->customer_profit;
                    $tot_margin_sc_month=$margin->partner_profit;
                    $tot_margin_hm_month=$margin->hm_profit;
                }
                
                $array_margin_total_year[intval($margin->mes-1)]=round(floatval($margin->customer_profit), 2);
                
                $array_margin_sc_year[intval($margin->mes-1)]=round(floatval($margin->partner_profit), 2);
                
                $array_margin_hm_year[intval($margin->mes-1)]=round(floatval($margin->hm_profit), 2);
            }

        }elseif(session('role')=='SOC'){

            $partner=Partner::where('user_id', Auth::user()->id)->first();
            
            $margins_year=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('mes')->get();
            
            $max_month=($margins_year->count()>0)?$margins_year->max('mes'):1;

            $array_margin_total_year = array_fill(0, $max_month, 0);
            $array_margin_sc_year = array_fill(0, $max_month, 0);
            $array_margin_hm_year = array_fill(0, $max_month, 0);
            
            foreach ($margins_year as $margin) {
                $tot_margin_sc_year+=$margin->partner_profit;
                ($margin->mes==$max_month)?$tot_margin_sc_month=$margin->partner_profit:'';
                $array_margin_sc_year[intval($margin->mes-1)]=round(floatval($margin->partner_profit), 2);
            }
        }
                
        return view('graph_my')
                    ->with('today', Carbon::now())
                    ->with('years', $years)
                    ->with('year', $year)                    
                    ->with('tot_margin_sc_year', $tot_margin_sc_year)
                    ->with('tot_margin_hm_year', $tot_margin_hm_year)                    
                    ->with('tot_margin_total_year', $tot_margin_total_year)
                    ->with('labels_months', json_encode($this->labels_months))
                    ->with('array_margin_total_year', json_encode($array_margin_total_year))
                    ->with('array_margin_sc_year', json_encode($array_margin_sc_year))
                    ->with('array_margin_hm_year', json_encode($array_margin_hm_year));
    }
}
