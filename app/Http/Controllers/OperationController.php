<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\OperationRequest;
use App\Models\Account;
use App\Models\Operation;
use App\Models\OperationType;
use App\Models\Center;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Partner;
use App\User;
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
use App\Mail\OperationNotification;

class OperationController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the operation.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');                
        
        $customers=Customer::orderBy('full_name')->pluck('full_name','id');
        $partners=Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->where('active',true)
                        ->orderBy('full_name')->pluck('full_name','partners.id');
        $users=User::where('role', 'MEN')->orderBy('full_name')->pluck('full_name','id');
        
        return view('operations.index')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('customers', $customers)
                        ->with('partners', $partners)
                        ->with('users', $users);
    }

    public function datatable(Request $request)
    {        
        $operations=$this->get_operations_collection($request);
        
        return Datatables::of($operations)
            ->addColumn('action', function ($operation) {
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalOperation('.$operation->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalComments('.$operation->id.')"><i class="fa fa-comments-o"></i> Comentarios</a>
                            </li>

                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$operation->id.'`, `'.$operation->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('partner', function ($operation) {                    
                    return $operation->partner->user->full_name;
                })
            ->editColumn('customer', function ($operation) {                    
                    return $operation->customer->full_name;
                })
            ->editColumn('date', function ($operation) {                    
                    return $operation->date->format('d/m/Y');
                })
            ->editColumn('amount', function ($operation) {                    
                    return '<div class="text-right">'.session('coin').money_fmt($operation->amount).'</div>';
                })
            ->editColumn('customer_profit', function ($operation) {                    
                    return '<div class="text-right">'.session('coin').money_fmt($operation->customer_profit).'<br>('.$operation->customer_tax.'%)</div>';
                })
            ->editColumn('partner_profit', function ($operation) {                    
                    return '<div class="text-right">'.session('coin').money_fmt($operation->partner_profit).'<br>('.$operation->partner_tax.'%)</div>';
                })
            ->editColumn('hm_profit', function ($operation) {                    
                    return '<div class="text-right">'.session('coin').money_fmt($operation->hm_profit).'<br>('.$operation->hm_tax.'%)</div>';
                })
            ->editColumn('status', function ($operation) {                    
                    if($operation->status==1){
                        return '<a href="#" onclick="showModalStatus('.$operation->id.')" title="Pasar a Pendiente">'.$operation->status_label.'</a>';
                    }elseif($operation->status==2){
                        return '<a href="#" onclick="showModalStatus('.$operation->id.')" title="Pasar a Entregado">'.$operation->status_label.'</a>';
                    }else{
                        return $operation->status_label;
                    }
                })
            ->rawColumns(['action', 'amount', 'customer_profit', 'partner_profit', 'hm_profit', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified operation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $setting=Setting::first();
        $customers=Customer::orderBy('full_name')->pluck('full_name','id');
        $partners=Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->where('active',true)
                        ->orderBy('full_name')->pluck('full_name','partners.id');
        $today=Carbon::now();

        if($id==0){
            $operation = new Operation();
        }else{
            $operation = Operation::find($id);
        }
        
        return view('operations.save')->with('operation', $operation)
                                ->with('today', $today)
                                ->with('setting', $setting)
                                ->with('customers', $customers)
                                ->with('partners', $partners);
    }

    /**
     * Store a newly created operation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OperationRequest $request)
    {
        try {
            $operation = new Operation();
            $operation->number=Operation::max('number')+1;
            $operation->customer_id=$request->customer;
            $operation->partner_id=$request->partner;
            $operation->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $operation->company=$request->company;
            $operation->folio=$request->folio;
            $operation->amount=$request->amount;
            $operation->customer_tax=$request->customer_tax;
            $operation->partner_tax=$request->partner_tax;
            $operation->hm_tax=$request->hm_tax;
            $operation->customer_profit=$operation->amount*($operation->customer_tax/100);
            $operation->partner_profit=$operation->customer_profit*($operation->partner_tax/100);
            $operation->hm_profit=$operation->customer_profit*($operation->hm_tax/100);
            $operation->notes=$request->notes;
            $operation->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Gasto registrado exitosamente',
                    'operation' => $operation->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified operation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OperationRequest $request, $id)
    {
        try {
            $operation = Operation::findOrFail($id);
            $operation->center_id=($request->center)?$request->center:null;
            $operation->operation_type_id=$request->operation_type;
            $operation->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $operation->concept=$request->concept;
            $operation->amount=$request->amount;
            $operation->reference=$request->reference;
            $operation->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($operation->file){
                    Storage::delete('operations/'.$operation->file);
                    Storage::delete('operations/thumbs/'.$operation->file);
                }
                $operation->file_name = $file->getClientOriginalName();
                $operation->file_type = $file->getClientOriginalExtension();
                $operation->file_size = $file->getSize();
                $operation->file=$this->upload_file('operations/', $file);
            }
            $operation->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Gasto actualizado exitosamente',
                    'operation' => $operation->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified operation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $operation = Operation::findOrFail($id);
            if($operation->file){
                Storage::delete('operations/'.$operation->file);
                Storage::delete('operations/thumbs/'.$operation->file);                
            }
            $operation->delete();
                        
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

    public function get_operations_collection(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter);
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter);;

        $operation_type_filter=$request->operation_type_filter;
        $center_filter=$request->center_filter;

        if($operation_type_filter!=''){
            if($center_filter!=''){
                $operations = Operation::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('operation_type_id', $operation_type_filter)
                                    ->where('center_id', $center_filter);
            }else{
                $operations = Operation::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('operation_type_id', $operation_type_filter);
            }
        }else{
            if($center_filter!=''){
                $operations = Operation::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter)
                                    ->where('center_id', $center_filter);
            }else{
                $operations = Operation::whereDate('date','>=', $start_filter)
                                    ->whereDate('date','<=', $end_filter);
            }
        }

        return $operations;
    }


    public function rpt_operations(Request $request){
                
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';
        
        $operations=$this->get_operations_collection($request)->get();

        if($request->center_filter!=''){
            $supplier=Supplier::find($request->center_filter);
            $supplier_name=$supplier->name;
        }else{
            $supplier_name='Todos';
        }

        if($request->operation_type_filter!=''){
            $operation_type=OperationType::find($request->operation_type_filter);
            $operation_type_name=$operation_type->name;
        }else{
            $operation_type_name='Todos';
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
            'operation_type_name' => $operation_type_name,
            'center_name' => $center_name,
            'operations' => $operations            
        ];

        $pdf = PDF::loadView('reports/rpt_operations', $data);
        
        return $pdf->stream('Gastos.pdf');        
    }

    /**
     * Display the specified operation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load_status($id)
    {
        $operation=Operation::findOrFail($id);
        $users=User::where('role', 'MEN')->pluck('full_name','id');
        
        return view('operations.status')->with('operation', $operation)
                                ->with('users', $users);
    }

    public function status(Request $request, $id)
    {
        try {
            $operation = Operation::findOrFail($id);
            if($operation->status==1){
                $operation->user_id=$request->user;                
                $operation->status=2;
            }elseif($operation->status==2){
                $operation->status=3;                
            }
            $operation->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Cambio de estado exitosamente',
                    'operation' => $operation->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Display the specified operation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load_comments($id)
    {
        $operation=Operation::findOrFail($id);

        return view('comments.save')->with('operation', $operation);
    }

}
