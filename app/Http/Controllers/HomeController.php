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

            $tot_incomes_year=0;
            $tot_incomes_month=0;
            $tot_incomes_day=0;
            $tot_expenses=0;
            $array_incomes_month = array_fill(0, $today->day, 0);
            $array_incomes_year = array_fill(0, $today->month, 0);
            $array_expenses = array_fill(0, $today->month, 0);
            
            $array_margin_total_month = array_fill(0, $today->day, 0);
            $array_margin_sc_month = array_fill(0, $today->day, 0);
            $array_margin_hm_month = array_fill(0, $today->day, 0);

            $tot_margin_total_year=0;
            $tot_margin_total_month=0;
            $tot_margin_sc_year=0;
            $tot_margin_sc_month=0;
            $tot_margin_hm_year=0;
            $tot_margin_hm_month=0;

            $array_margin_total_year = array_fill(0, $today->month, 0);
            $array_margin_sc_year = array_fill(0, $today->month, 0);
            $array_margin_hm_year = array_fill(0, $today->month, 0);

            $labels_days=["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"];
            
            $labels_months=["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];


        if(session('role')=='ADM'){
            
            $tot_customers=Customer::all()->count();
            $tot_operations=Operation::all()->count();

            $incomes_month=Operation::
                            whereYear('date', '=', $today->year)
                            ->whereMonth('date', '=', $today->month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('dia')->get();

            foreach ($incomes_month as $income) {
                $tot_incomes_month+=$income->amount;
                ($income->dia==$today->day)?$tot_incomes_day=$income->amount:'';
                $array_incomes_month[intval($income->dia-1)]=round(floatval($income->amount), 2);
            }
            

            $incomes_year=Operation::
                            whereYear('date', '=', $today->year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('mes')->get();

            foreach ($incomes_year as $income) {
                $tot_incomes_year+=$income->amount;
                $array_incomes_year[intval($income->mes-1)]=round(floatval($income->amount), 2);
            }

            
            $margins_month=Operation::
                            whereYear('date', '=', $today->year)
                            ->whereMonth('date', '=', $today->month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('dia')->get();
            
            foreach ($margins_month as $margin) {
                $array_margin_total_month[intval($margin->dia-1)]=round(floatval($margin->customer_profit), 2);
                $array_margin_sc_month[intval($margin->dia-1)]=round(floatval($margin->partner_profit), 2);
                $array_margin_hm_month[intval($margin->dia-1)]=round(floatval($margin->hm_profit), 2);
            }
            
            $margins_year=Operation::
                            whereYear('date', '=', $today->year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('mes')->get();
            
            foreach ($margins_year as $margin) {
                $tot_margin_total_year+=$margin->customer_profit;
                $tot_margin_sc_year+=$margin->partner_profit;
                $tot_margin_hm_year+=$margin->hm_profit;

                if($margin->mes==$today->month){
                    $tot_margin_total_month=$margin->customer_profit;
                    $tot_margin_sc_month=$margin->partner_profit;
                    $tot_margin_hm_month=$margin->hm_profit;
                }
                
                $array_margin_total_year[intval($margin->mes-1)]=round(floatval($margin->customer_profit), 2);
                
                $array_margin_sc_year[intval($margin->mes-1)]=round(floatval($margin->partner_profit), 2);
                
                $array_margin_hm_year[intval($margin->mes-1)]=round(floatval($margin->hm_profit), 2);
            }
            
            return view('home')->with('today', $today)
                        ->with('tot_customers', $tot_customers)
                        ->with('tot_operations', $tot_operations)
                        ->with('tot_incomes_month', $tot_incomes_month)
                        ->with('tot_incomes_year', $tot_incomes_year)
                        ->with('tot_expenses_month', 0)
                        ->with('labels_days', json_encode($labels_days))
                        ->with('labels_months', json_encode($labels_months))
                        ->with('tot_margin_total_year', $tot_margin_total_year)
                        ->with('tot_margin_total_month', $tot_margin_total_month)
                        ->with('tot_margin_sc_year', $tot_margin_sc_year)
                        ->with('tot_margin_sc_month', $tot_margin_sc_month)
                        ->with('tot_margin_hm_year', $tot_margin_hm_year)
                        ->with('tot_margin_hm_month', $tot_margin_hm_month)
                        ->with('array_incomes_month', json_encode($array_incomes_month))
                        ->with('array_incomes_year', json_encode($array_incomes_year))
                        ->with('array_margin_total_month', json_encode($array_margin_total_month))
                        ->with('array_margin_sc_month', json_encode($array_margin_sc_month))
                        ->with('array_margin_hm_month', json_encode($array_margin_hm_month))
                        ->with('array_margin_total_year', json_encode($array_margin_total_year))
                        ->with('array_margin_sc_year', json_encode($array_margin_sc_year))
                        ->with('array_margin_hm_year', json_encode($array_margin_hm_year))
                        ->with('array_expenses', json_encode($array_expenses));

        }elseif(session('role')=='SOC'){

            $partner=Partner::where('user_id', Auth::user()->id)->first();

            $tot_customers=Customer::where('partner_id', $partner->id)->count();
            $tot_operations=Operation::where('partner_id', $partner->id)->count();

            $incomes_month=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $today->year)
                            ->whereMonth('date', '=', $today->month)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%d") as dia'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('dia')->get();

            foreach ($incomes_month as $income) {
                $tot_incomes_month+=$income->amount;
                ($income->dia==$today->day)?$tot_incomes_day=$income->amount:'';
                $array_incomes_month[intval($income->dia-1)]=round(floatval($income->amount), 2);
            }
            

            $incomes_year=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $today->year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(amount) as amount"))
                            ->groupBy('mes')->get();

            foreach ($incomes_year as $income) {
                $tot_incomes_year+=$income->amount;
                $array_incomes_year[intval($income->mes-1)]=round(floatval($income->amount), 2);
            }

            
            $margins_month=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $today->year)
                            ->whereMonth('date', '=', $today->month)
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
            
            $tot_margin_sc_month=0;
            $tot_margin_sc_year=0;

            $margins_year=Operation::
                            where('partner_id', $partner->id)
                            ->whereYear('date', '=', $today->year)
                            ->select(
                                DB::raw('DATE_FORMAT(date, "%m") as mes'),
                                DB::raw("sum(customer_profit) as customer_profit"),
                                DB::raw("sum(partner_profit) as partner_profit"),
                                DB::raw("sum(hm_profit) as hm_profit"),
                            )
                            ->groupBy('mes')->get();
            
            foreach ($margins_year as $margin) {
                $tot_margin_sc_year+=$margin->partner_profit;
                ($margin->mes==$today->month)?$tot_margin_sc_month=$margin->partner_profit:'';
                $array_margin_sc_year[intval($margin->mes-1)]=round(floatval($margin->partner_profit), 2);
            }
            
            return view('home_sc')->with('today', $today)
                        ->with('tot_customers', $tot_customers)
                        ->with('tot_operations', $tot_operations)
                        ->with('tot_incomes_month', $tot_incomes_month)
                        ->with('tot_incomes_year', $tot_incomes_year)
                        ->with('tot_margin_sc_month', $tot_margin_sc_month)
                        ->with('tot_margin_sc_year', $tot_margin_sc_year)
                        ->with('labels_days', json_encode($labels_days))
                        ->with('array_incomes_month', json_encode($array_incomes_month))
                        ->with('array_incomes_year', json_encode($array_incomes_year))
                        ->with('labels_months', json_encode($labels_months))
                        ->with('array_margin_sc_month', json_encode($array_margin_sc_month))
                        ->with('array_margin_sc_year', json_encode($array_margin_sc_year));

        
        }elseif(session('role')=='SUP'){
            
            return redirect()->route('operations.index');

        }elseif(session('role')=='MEN'){
            
            return redirect()->route('operations.index');
        }
    }
}
