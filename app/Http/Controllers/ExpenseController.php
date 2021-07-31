<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ExpenseRequest;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Movement;
use App\Models\Supplier;
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
        $this->middleware(function ($request, $next) {
            $this->condominium=session()->get('condominium');
            return $next($request);
        });    

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
        
        $expense_types = ExpenseType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->pluck('name','id');
        
        $suppliers=$this->condominium->suppliers()->orderBy('name')->pluck('name','id');
        
        return view('expenses.index')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('expense_types', $expense_types)
                        ->with('suppliers', $suppliers);
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
                    if($expense->supplier_id){
                        return '<a href="#"  onclick="showModalExpense('.$expense->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$expense->supplier->name.'<br><small>'.$expense->concept.'<br><i>'.$expense->expense_type->name.'</i></small></a>';
                    }else{
                        return '<a href="#"  onclick="showModalExpense('.$expense->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$expense->concept.'<br><small><i>'.$expense->expense_type->name.'</i></small></a>';
                    }
                })
            ->editColumn('account', function ($expense) {                    
                    return $expense->account->aliase.'<br><small><i>'.$expense->payment_method_description.'</i></small>';
                })
            ->editColumn('date', function ($expense) {                    
                    return $expense->date->format('d/m/Y');
                })
            ->editColumn('amount', function ($expense) {                    
                    return money_fmt($expense->amount);
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
        $expense_types = ExpenseType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->pluck('name','id');
        
        $accounts=$this->condominium->accounts()->orderBy('aliase')->pluck('aliase','id');
        $projects=$this->condominium->projects()->orderBy('name')->pluck('name','id');
        $suppliers=$this->condominium->suppliers()->where('active',true)->orderBy('name')->pluck('name','id');

        $today=Carbon::now();

        if($id==0){
            $expense = new Expense();
        }else{
            $expense = Expense::find($id);
        }
        
        return view('expenses.save')->with('expense', $expense)
                                ->with('today', $today)
                                ->with('expense_types', $expense_types)
                                ->with('accounts', $accounts)
                                ->with('projects', $projects)
                                ->with('suppliers', $suppliers);
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
            $expense->condominium_id=$request->condominium_id;
            $expense->supplier_id=($request->supplier)?$request->supplier:null;
            $expense->project_id=($request->project)?$request->project:null;
            $expense->expense_type_id=$request->expense_type;
            $expense->account_id=$request->account;
            $expense->payment_method=$request->payment_method;
            $expense->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $expense->amount=$request->amount;
            $expense->concept=$request->concept;
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
            //se registra el movimiento
            Movement::create([
                'type' => 'D',
                'account_id' => $expense->account_id,
                'expense_id' => $expense->id,
                'date' => $expense->date,
                'amount' => $expense->amount
            ]);
            //se actualizan los saldos de la cuenta
            $expense->account->update_balance();


            if($request->notification && $expense->supplier_id && $expense->supplier->email){
                Mail::to($expense->supplier->email)->send(new ExpenseNotification($expense));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Egreso registrado exitosamente',
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
            $expense = Expense::find($id);
            $expense->condominium_id=$request->condominium_id;
            $expense->supplier_id=($request->supplier)?$request->supplier:null;
            $expense->project_id=($request->project)?$request->project:null;
            $expense->expense_type_id=$request->expense_type;
            $expense->account_id=$request->account;
            $expense->payment_method=$request->payment_method;
            $expense->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $expense->amount=$request->amount;
            $expense->concept=$request->concept;
            $expense->reference=$request->reference;
            $expense->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($expense->file){
                    Storage::delete($expense->condominium_id.'/expenses/'.$expense->file);
                    Storage::delete($expense->condominium_id.'/expenses/thumbs/'.$expense->file);
                }
                $expense->file_name = $file->getClientOriginalName();
                $expense->file_type = $file->getClientOriginalExtension();
                $expense->file_size = $file->getSize();
                $expense->file=$this->upload_file($expense->condominium_id.'/expenses/', $file);
            }
            $expense->save();
            //se actualiza el movimiento
            $expense->movement->account_id=$expense->account_id;
            $expense->movement->date=$expense->date;
            $expense->movement->amount=$expense->amount;
            $expense->movement->save();
            //se actualizan los saldos de la cuenta
            $expense->account->update_balance();
            
            if($request->notification && $expense->supplier_id && $expense->supplier->email){
                Mail::to($expense->supplier->email)->send(new ExpenseNotification($expense));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Egreso actualizado exitosamente',
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
            $expense = Expense::find($id);
            $account=Account::find($expense->account_id);
            
            Storage::delete($expense->condominium_id.'/expenses/'.$expense->file);
            Storage::delete($expense->condominium_id.'/expenses/thumbs/'.$expense->file);
            $expense->delete();
            
            //se actualiza el saldo de la cuenta
            $account->update_balance();
            
            return response()->json([
                'success' => true,
                'message' => 'Egreso eliminado exitosamente'
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
        $supplier_filter=$request->supplier_filter;

        if($expense_type_filter!=''){
            if($supplier_filter!=''){
                $expenses = $this->condominium->expenses()
                                ->whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('expense_type_id', $expense_type_filter)
                                ->where('supplier_id', $supplier_filter);
            }else{
                $expenses = $this->condominium->expenses()
                                ->whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('expense_type_id', $expense_type_filter);
            }
        }else{
            if($supplier_filter!=''){
                $expenses = $this->condominium->expenses()
                                ->whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('supplier_id', $supplier_filter);
            }else{
                $expenses = $this->condominium->expenses()
                                ->whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter);
            }
        }

        return $expenses;
    }


    public function rpt_expenses(Request $request){
                
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $expenses=$this->get_expenses_collection($request)->get();

        if($request->supplier_filter!=''){
            $supplier=Supplier::find($request->supplier_filter);
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
        
        $data=[
            'company' => $this->condominium->name,
            'logo' => $logo,            
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'supplier_name' => $supplier_name,
            'expense_type_name' => $expense_type_name,
            'expenses' => $expenses            
        ];

        $pdf = PDF::loadView('reports/rpt_expenses', $data);
        
        return $pdf->stream('Egresos extraordinarios.pdf');        
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
