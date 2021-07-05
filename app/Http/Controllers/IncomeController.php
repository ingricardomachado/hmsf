<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\IncomeRequest;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Movement;
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

class IncomeController extends Controller
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
     * Display a listing of the income.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('incomes.index');
    }

    public function datatable()
    {        
        /*Se construye asi para que funcione el search de Yajra*/
        $incomes = $this->condominium->incomes();
        
        return Datatables::of($incomes)
            ->addColumn('action', function ($income) {
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalIncome('.$income->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$income->id.'`, `'.$income->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('income', function ($income) {                    
                    return '<a href="#"  onclick="showModalIncome('.$income->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$income->concept.'<br><small><i>'.$income->income_type->name.'</i></small></a>';
                })
            ->editColumn('account', function ($income) {                    
                    return $income->account->aliase.'<br><small><i>'.$income->payment_method_description.'</i></small>';
                })
            ->editColumn('date', function ($income) {                    
                    return $income->date->format('d/m/Y');
                })
            ->editColumn('amount', function ($income) {                    
                    return money_fmt($income->amount);
                })
            ->addColumn('file', function ($income) {
                    if($income->file_name){                    
                        $ext=$income->file_type;
                        if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                            $url_show_file = url('income_image', $income->id);
                            return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$income->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
                        }else{
                            $url_download_file = route('incomes.download', $income->id);
                            return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$income->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
                        }
                    }
                })
            ->rawColumns(['action', 'income', 'account', 'file'])
            ->make(true);
    }
    
    /**
     * Display the specified income.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->pluck('name','id');
        
        $accounts=$this->condominium->accounts()->orderBy('aliase')->pluck('aliase','id');
        $projects=$this->condominium->projects()->orderBy('name')->pluck('name','id');

        $today=Carbon::now();

        if($id==0){
            $income = new Income();
        }else{
            $income = Income::find($id);
        }
        
        return view('incomes.save')->with('income', $income)
                                ->with('today', $today)
                                ->with('income_types', $income_types)
                                ->with('accounts', $accounts)
                                ->with('projects', $projects);
    }

    /**
     * Store a newly created income in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IncomeRequest $request)
    {
        try {
            $income = new Income();
            $income->condominium_id=$request->condominium_id;
            $income->project_id=($request->project)?$request->project:null;
            $income->income_type_id=$request->income_type;
            $income->account_id=$request->account;
            $income->payment_method=$request->payment_method;
            $income->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $income->amount=$request->amount;
            $income->concept=$request->concept;
            $income->reference=$request->reference;
            $income->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                $income->file_name = $file->getClientOriginalName();
                $income->file_type = $file->getClientOriginalExtension();
                $income->file_size = $file->getSize();
                $income->file=$this->upload_file($income->condominium_id.'/incomes/', $file);
            }
            $income->save();
            //se registra el movimiento
            Movement::create([
                'type' => 'C',
                'account_id' => $income->account_id,
                'income_id' => $income->id,
                'date' => $income->date,
                'amount' => $income->amount
            ]);
            //se actualizan los saldos de la cuenta
            $income->account->update_balance();                       
            
            return response()->json([
                    'success' => true,
                    'message' => 'Ingreso extraordinario registrado exitosamente',
                    'income' => $income->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified income in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IncomeRequest $request, $id)
    {
        try {
            $income = Income::find($id);
            $income->condominium_id=$request->condominium_id;
            $income->project_id=($request->project)?$request->project:null;            
            $income->income_type_id=$request->income_type;
            $income->account_id=$request->account;
            $income->payment_method=$request->payment_method;
            $income->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $income->amount=$request->amount;
            $income->concept=$request->concept;
            $income->reference=$request->reference;
            $income->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($income->file){
                    Storage::delete($income->condominium_id.'/incomes/'.$income->file);
                    Storage::delete($income->condominium_id.'/incomes/thumbs/'.$income->file);
                }
                $income->file_name = $file->getClientOriginalName();
                $income->file_type = $file->getClientOriginalExtension();
                $income->file_size = $file->getSize();
                $income->file=$this->upload_file($income->condominium_id.'/incomes/', $file);
            }
            $income->save();
            //se actualiza el movimiento
            $income->movement->account_id=$income->account_id;
            $income->movement->date=$income->date;
            $income->movement->amount=$income->amount;
            $income->movement->save();
            //se actualizan los saldos de la cuenta
            $income->account->update_balance();            
            
            return response()->json([
                    'success' => true,
                    'message' => 'Ingreso extraordinario actualizado exitosamente',
                    'income' => $income->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified income from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $income = Income::find($id);
            Storage::delete($income->condominium_id.'/incomes/'.$income->file);
            Storage::delete($income->condominium_id.'/incomes/thumbs/'.$income->file);

            $income->delete();
            $income->account->update_balance();
            
            return response()->json([
                'success' => true,
                'message' => 'Ingreso extraordinario eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
     * Download file from DB  
    */ 
    public function download_file($id)
    {
        $income = Document::find($id);
        return response()->download(storage_path('app/'.$income->condominium_id.'/incomes/'.$income->file), $income->file_name);
    }
    
}
