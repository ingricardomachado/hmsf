<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\FeeRequest;
use App\Models\Fee;
use App\Models\Property;
use App\Models\IncomeType;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use App\Exports\PropertiesExport;
use Carbon\Carbon;
use Storage;
use Image;
use File;
use DB;
use PDF;
use Auth;

class FeeController extends Controller
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
     * Display a listing of the fee.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->orderBy('name')->pluck('name','id');        
        
        return view('fees.index')->with('properties', $properties)
                            ->with('income_types', $income_types);
    }

    public function datatable(Request $request)
    {        
        $property_filter=$request->property_filter;
        $income_type_filter=$request->income_type_filter;

        if($income_type_filter!=''){
            if($property_filter!=''){
                $fees = $this->condominium->fees()
                        ->where('income_type_id', $income_type_filter)
                        ->where('property_id', $property_filter)
                        ->where('balance', '>', 0);
            }else{
                $fees = $this->condominium->fees()
                        ->where('income_type_id', $income_type_filter)
                        ->where('balance', '>', 0);
            }
        }else{
            if($property_filter!=''){
                $fees = $this->condominium->fees()
                        ->where('property_id', $property_filter)
                        ->where('balance', '>', 0);
            }else{
                $fees = $this->condominium->fees()
                        ->where('balance', '>', 0);
            }
        }
                
        return Datatables::of($fees)
            ->addColumn('action', function ($fee) {
                if($fee->payments->count()){
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" class="modal-class" onclick="showModalFeeInfo('.$fee->id.')"><i class="fa fa-laptop"></i> Ver Detalle</a>
                            </li>
                        </ul>
                    </div>';
                }else{
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" class="modal-class" onclick="showModalFee('.$fee->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li>
                                <a href="#" class="modal-class" onclick="showModalFeeInfo('.$fee->id.')"><i class="fa fa-laptop"></i> Ver Detalle</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$fee->id.'`, `'.$fee->concept.'`, `'.$fee->property->number.'`, `'.$fee->amount.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                }
                })           
            ->editColumn('fee', function ($fee) {                    
                    if($fee->payments->count()){
                        return '<a href="#" onclick="showModalFeeInfo('.$fee->id.')" style="color:inherit" title="Click para ver detalle">'.$fee->concept.'<br><small><i>'.$fee->income_type->name.'</small></i></a>';
                    }else{
                        return '<a href="#"  onclick="showModalFee('.$fee->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$fee->concept.'<br><small><i>'.$fee->income_type->name.'</small></i></a>';
                    }
                })
            ->editColumn('date', function ($fee) {                    
                    return $fee->date->format('d/m/Y');
                })
            ->editColumn('due_date', function ($fee) {                    
                    return $fee->due_date->format('d/m/Y').'<br><small><i>'.$fee->remainig_days_description.'</i></small>';
                })
            ->addColumn('property', function ($fee) {                    
                    return $fee->property->number;
                })
            ->editColumn('amount', function ($fee) {                    
                    return money_fmt($fee->amount);
                })
            ->editColumn('paid', function ($fee) {                    
                    $paid=$fee->payments()->where('status','A')->sum('payment_fee.amount');
                    return ($paid>0)?money_fmt($paid):'';
                })
            ->editColumn('balance', function ($fee) {                    
                    return ($fee->balance>0)?money_fmt($fee->balance):'';
                })
            ->editColumn('status', function ($fee) {                    
                    return $fee->status_label;
                })
            ->rawColumns(['action', 'fee', 'date', 'due_date', 'status'])
            ->make(true);
    }
    
    public function info($id){

        $fee=Fee::findOrFail($id);

        return view('fees.info')->with('fee', $fee);
    }
    
    public function create_multiple()
    {
        $properties=$this->condominium->properties()
                    ->leftjoin('users', 'properties.user_id', '=', 'users.id')
                    ->select('properties.id', 'properties.number', 'properties.tax', 'users.name as user')
                    ->get();
                    
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->orderBy('name')->pluck('name','id');
        $projects=$this->condominium->projects()->orderBy('name')->pluck('name','id');

        $today=Carbon::now()->format('d/m/Y');
        $last_day_of_month=Carbon::now()->lastOfMonth()->format('d/m/Y'); 
        
        $fee = new Fee();
        
        return view('fees.create_multiple')->with('fee', $fee)
                        ->with('today', $today)
                        ->with('last_day_of_month', $last_day_of_month)
                        ->with('income_types', $income_types)
                        ->with('properties', $properties)
                        ->with('projects', $projects);
    }

    /**
     * Display the specified fee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->orderBy('name')->pluck('name','id');
        $projects=$this->condominium->projects()->orderBy('name')->pluck('name','id');        

        $today=Carbon::now()->format('d/m/Y');
        $last_day_of_month=Carbon::now()->lastOfMonth()->format('d/m/Y'); 
        
        if($id==0){
            $fee = new Fee();
        }else{
            $fee = Fee::find($id);
        }
        
        return view('fees.save')->with('fee', $fee)
                        ->with('today', $today)
                        ->with('last_day_of_month', $last_day_of_month)
                        ->with('income_types', $income_types)
                        ->with('properties', $properties)
                        ->with('projects', $projects);
    }

    
    /**
     * Store a newly created fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeeRequest $request)
    {
        try {
            $fee = new Fee();
            $fee->created_by=Auth::user()->name;
            $fee->condominium_id=$request->condominium_id;
            $fee->income_type_id=$request->income_type;
            $fee->property_id=$request->property;
            $fee->project_id=($request->project)?$request->project:null;
            $fee->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $fee->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date);
            $fee->concept=$request->concept;
            $fee->amount=$request->amount;
            $fee->balance=$fee->amount;
            $fee->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Cuota registrada exitosamente',
                    'fee' => $fee->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    /**
     * Store a newly created fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_multiple(FeeRequest $request)
    {
        try {
            
            $distribution_type=$request->distribution_type;
            $amount=$request->amount;
            $array_properties=$request->array_properties;
            $array_amounts=$request->array_amounts;
            for ($i=0; $i < count($array_properties); $i++) { 
                $fee = new Fee();
                $fee->created_by=Auth::user()->name;
                $fee->condominium_id=$request->condominium_id;
                $fee->income_type_id=$request->income_type;
                $fee->project_id=($request->project)?$request->project:null;
                $fee->property_id=$array_properties[$i];
                $fee->date=Carbon::createFromFormat('d/m/Y', $request->date);
                $fee->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date);
                $fee->concept=$request->concept;
                switch ($distribution_type) {
                    case '1':
                        $fee->amount=$amount;
                        break;
                    case '2':
                        $fee->amount=$amount/count($array_properties);
                        break;
                    case '3':
                        $property=Property::find($array_properties[$i]);
                        $fee->amount=$amount*($property->tax/100);
                        break;
                    case '4':
                        $fee->amount=$array_amounts[$i];
                        break;
                }
                $fee->balance=$fee->amount;
                $fee->save();
            }

            return response()->json([
                    'success' => true,
                    'message' => 'Cuotas registradas exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

   /**
     * Update the specified fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FeeRequest $request, $id)
    {
        try {
            $fee = Fee::find($id);
            $fee->income_type_id=$request->income_type;
            $fee->property_id=$request->property;
            $fee->project_id=($request->project)?$request->project:null;
            $fee->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $fee->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date);
            $fee->concept=$request->concept;
            $fee->amount=$request->amount;
            $fee->balance=$fee->amount;
            $fee->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Cuota actualizada exitosamente',
                    'fee' => $fee
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified fee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $fee = Fee::find($id);
            Storage::delete($fee->condominium_id.'/fees/'.$fee->file);
            Storage::delete($fee->condominium_id.'/fees/thumbs/'.$fee->file);
            $fee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cuota eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
