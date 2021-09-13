<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ExpenseRequest;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Center;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Illuminate\Support\Facades\Validator;
//Export
use App\Exports\PropertiesExport;
use Storage;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Carbon\Carbon;
use Mail;
use App\Mail\ExpenseNotification;

class ExpenseController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');                
        
        $expense_types=ExpenseType::orderBy('name')->pluck('name','id');
        $centers=Center::orderBy('name')->pluck('name','id');
        
        return view('expenses.index')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('expense_types', $expense_types)
                        ->with('centers', $centers);
    }

    public function datatable(Request $request)
    {        
        $expenses=$this->get_expenses_collection($request);
        
        return Datatables::of($expenses)
            ->addColumn('action', function ($expense) {
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalExpense('.$expense->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$expense->id.'`, `'.$expense->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('expense', function ($expense) {                    
                        return '<a href="#"  onclick="showModalExpense('.$expense->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$expense->concept.'<br><small><i>'.$expense->expense_type->name.'</i></small></a>';
                })
            ->editColumn('center', function ($expense) {                    
                    return ($expense->center_id)?$expense->center->name:null;
                })
            ->editColumn('date', function ($expense) {                    
                    return $expense->date->format('d/m/Y');
                })
            ->addColumn('file', function ($expense) {
                    return $expense->download_file;
                })
            ->rawColumns(['action', 'expense', 'account', 'file'])
            ->make(true);
    }
    
    /**
     * Display the specified expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $expense_types=ExpenseType::orderBy('name')->pluck('name','id');
        $centers=Center::orderBy('name')->pluck('name','id');
        $today=Carbon::now();

        if($id==0){
            $expense = new Expense();
        }else{
            $expense = Expense::find($id);
        }
        
        return view('expenses.save')->with('expense', $expense)
                                ->with('today', $today)
                                ->with('expense_types', $expense_types)
                                ->with('centers', $centers);
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseRequest $request)
    {
        try {
            $expense = new Expense();
            $expense->center_id=($request->center)?$request->center:null;
            $expense->expense_type_id=$request->expense_type;
            $expense->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $expense->concept=$request->concept;
            $expense->amount=$request->amount;
            $expense->reference=$request->reference;
            $expense->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                $expense->file_name = $file->getClientOriginalName();
                $expense->file_type = $file->getClientOriginalExtension();
                $expense->file_size = $file->getSize();
                $expense->file=$this->upload_file($expense->condominium_id.'/expenses/', $file);
            }
            $expense->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Gasto registrado exitosamente',
                    'expense' => $expense->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseRequest $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->center_id=($request->center)?$request->center:null;
            $expense->expense_type_id=$request->expense_type;
            $expense->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $expense->concept=$request->concept;
            $expense->amount=$request->amount;
            $expense->reference=$request->reference;
            $expense->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($expense->file){
                    Storage::delete('expenses/'.$expense->file);
                    Storage::delete('expenses/thumbs/'.$expense->file);
                }
                $expense->file_name = $file->getClientOriginalName();
                $expense->file_type = $file->getClientOriginalExtension();
                $expense->file_size = $file->getSize();
                $expense->file=$this->upload_file('expenses/', $file);
            }
            $expense->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Gasto actualizado exitosamente',
                    'expense' => $expense->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            if($expense->file){
                Storage::delete('expenses/'.$expense->file);
                Storage::delete('expenses/thumbs/'.$expense->file);                
            }
            $expense->delete();
                        
            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function get_expenses_collection(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter);
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter);;

        $expense_type_filter=$request->expense_type_filter;
        $center_filter=$request->center_filter;

        if($expense_type_filter!=''){
            if($center_filter!=''){
                $expenses = Expense::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('expense_type_id', $expense_type_filter)
                                    ->where('center_id', $center_filter);
            }else{
                $expenses = Expense::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('expense_type_id', $expense_type_filter);
            }
        }else{
            if($center_filter!=''){
                $expenses = Expense::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('center_id', $center_filter);
            }else{
                $expenses = Expense::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter);
            }
        }

        return $expenses;
    }


    public function rpt_expenses(Request $request){
                
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';
        
        $expenses=$this->get_expenses_collection($request)->get();

        if($request->center_filter!=''){
            $supplier=Supplier::find($request->center_filter);
            $supplier_name=$supplier->name;
        }else{
            $supplier_name='Todos';
        }

        if($request->expense_type_filter!=''){
            $expense_type=ExpenseType::find($request->expense_type_filter);
            $expense_type_name=$expense_type->name;
        }else{
            $expense_type_name='Todos';
        }
        
        if($request->center_filter!=''){
            $center=Center::find($request->center_filter);
            $center_name=$center->name;
        }else{
            $center_name='Todos';
        }

        $data=[
            'company' => $setting->company,
            'logo' => $logo,            
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'supplier_name' => $supplier_name,
            'expense_type_name' => $expense_type_name,
            'center_name' => $center_name,
            'expenses' => $expenses            
        ];

        $pdf = PDF::loadView('reports/rpt_expenses', $data);
        
        return $pdf->stream('Gastos.pdf');        
    }    
    
    /*
     * Download file from DB  
    */ 
    public function download_file($id)
    {
        $expense = Expense::find($id);
        
        return Storage::download($expense->condominium_id.'/expenses/'.$expense->file, $expense->file_name);
    }
}
