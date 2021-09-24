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
use App\Models\Company;
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
use App\Mail\AssignedOperation;

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
        
        if(Auth::user()->role=='ADM'){
            return view('operations.adm')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('customers', $customers)
                        ->with('partners', $partners)
                        ->with('users', $users);
        }elseif(Auth::user()->role=='SOC'){
            return view('operations.soc')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('customers', $customers)
                        ->with('partners', $partners)
                        ->with('users', $users);
        }elseif(Auth::user()->role=='SUP'){
            return view('operations.sup')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('customers', $customers)
                        ->with('partners', $partners)
                        ->with('users', $users);
        }elseif(Auth::user()->role=='MEN'){
            return view('operations.men')
                        ->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('customers', $customers)
                        ->with('partners', $partners)
                        ->with('users', $users);
        }
    }

    public function datatable(Request $request)
    {        
        $operations=$this->get_operations_collection($request);
        
        return Datatables::of($operations)
            ->addColumn('action', function ($operation) {
                if(Auth::user()->role=='ADM'){
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
                }else{
                    return "";
                }    
                })           
            ->editColumn('number', function ($operation) {                    
                    return '<a href="#"  onclick="showModalComments('.$operation->id.')" class="modal-class" style="color:inherit"  title="Click para comentarios"><b>'.$operation->number.'</b></a>';
                })
            ->editColumn('partner', function ($operation) {                    
                    return $operation->partner->user->full_name;
                })
            ->editColumn('customer', function ($operation) {                    
                    if(Auth::user()->role=='SOC'){
                        if($operation->customer->contract){
                            return $operation->customer->code.'<br>Contrato '.$operation->customer->contract;
                        }else{
                            return $operation->customer->code;
                        }
                    }else{
                        if($operation->customer->contract){
                            return $operation->customer->full_name.'<br><span class="text-muted">'.$operation->customer->code.'</span><br>Contrato '.$operation->customer->contract;
                        }else{
                            return $operation->customer->full_name.'<br><span class="text-muted">'.$operation->customer->code.'</span>';                
                        }
                    }
                })
            ->editColumn('user', function ($operation) {                    
                    return ($operation->user_id)?$operation->user->full_name:'';
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
            ->editColumn('return_amount', function ($operation) {                    
                    return '<div class="text-right">'.session('coin').money_fmt($operation->return_amount).'</div>';
                })
            ->editColumn('status', function ($operation) {                    
                    if($operation->status==1 && (Auth::user()->role=='ADM' || Auth::user()->role=='MEN')){
                        return '<a href="#" onclick="showModalStatus('.$operation->id.')">'.$operation->status_label.'</a>';
                    }elseif($operation->status==2 && (Auth::user()->role=='ADM' || Auth::user()->role=='MEN')){
                        return '<a href="#" onclick="showModalStatus('.$operation->id.')">'.$operation->status_label.'</a>';
                    }else{
                        return $operation->status_label;
                    }
                })
            ->rawColumns(['action', 'number', 'customer', 'amount', 'customer_profit', 'partner_profit', 'hm_profit', 'return_amount', 'status'])
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
        $companies=Company::orderBy('name')->pluck('name','id');
        $users=User::where('role', 'MEN')->orderBy('full_name')->pluck('full_name','id');
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
                                ->with('companies', $companies)
                                ->with('users', $users)
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
            $operation->company_id=$request->company;
            $operation->user_id=$request->user;
            $operation->folio=$request->folio;
            $operation->amount=$request->amount;
            $operation->customer_tax=$request->customer_tax;
            $operation->partner_tax=$request->partner_tax;
            $operation->hm_tax=$request->hm_tax;
            $operation->customer_profit=$operation->amount*($operation->customer_tax/100);
            $operation->partner_profit=$operation->customer_profit*($operation->partner_tax/100);
            $operation->hm_profit=$operation->customer_profit*($operation->hm_tax/100);
            $operation->return_amount=$operation->amount-$operation->customer_profit;
            $operation->save();
            if($request->notification){
                Mail::to($operation->user->email)->send(new AssignedOperation($operation));
            }

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
            $operation->customer_id=$request->customer;
            $operation->partner_id=$request->partner;
            $operation->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $operation->company_id=$request->company;
            $operation->user_id=$request->user;
            $operation->folio=$request->folio;
            $operation->amount=$request->amount;
            $operation->customer_tax=$request->customer_tax;
            $operation->partner_tax=$request->partner_tax;
            $operation->hm_tax=$request->hm_tax;
            $operation->customer_profit=$operation->amount*($operation->customer_tax/100);
            $operation->partner_profit=$operation->customer_profit*($operation->partner_tax/100);
            $operation->hm_profit=$operation->customer_profit*($operation->hm_tax/100);
            $operation->return_amount=$operation->amount-$operation->customer_profit;
            $operation->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Operación actualizado exitosamente',
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

        if(Auth::user()->role=='SOC'){
            $partner=Partner::where('user_id', Auth::user()->id)->first();
            $partner_filter=$partner->id;
        }else{
            $partner_filter=$request->partner_filter;
        }

        if(Auth::user()->role=='MEN'){
            $user_filter=Auth::user()->id;
        }else{
            $user_filter=$request->user_filter;
        }

        $customer_filter=$request->customer_filter;
        $status_filter=$request->status_filter;

        if($partner_filter!=''){
            if($customer_filter!=''){
                if($user_filter!=''){
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('user_id', $user_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('user_id', $user_filter);
                    }
                }else{
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('customer_id', $customer_filter);
                    }
                }
            }else{
                if($user_filter!=''){
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('user_id', $user_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('user_id', $user_filter);
                    }
                }else{
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('partner_id', $partner_filter);
                    }
                }
            }
        }else{
            if($customer_filter!=''){
                if($user_filter!=''){
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('user_id', $user_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('user_id', $user_filter);
                    }
                }else{
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('customer_id', $customer_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('customer_id', $customer_filter);
                    }
                }
            }else{
                if($user_filter!=''){
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('user_id', $user_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('user_id', $user_filter);
                    }
                }else{
                    if($status_filter!=''){
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)
                                ->where('status', $status_filter);
                    }else{
                        $operations = Operation::whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter);
                    }
                }
            }
        }

        return $operations;
    }


    public function rpt_operations(Request $request){
                
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';
        
        $operations=$this->get_operations_collection($request)->get();

        $partner=null;
        $customer=null;
        $user=null;
        $status=null;
        
        if($request->partner_filter!=''){
            $partner=Partner::findOrFail($request->partner_filter);
        }elseif(Auth::user()->role=='SOC'){
            $partner=Partner::where('user_id', Auth::user()->id)->first();
        }

        if($request->customer_filter!=''){
            $customer=Customer::findOrFail($request->customer_filter);
        }

        if($request->user_filter!=''){
            $user=User::findOrFail($request->user_filter);
        }elseif(Auth::user()->role=='MEN'){
            $user=Auth::user();
        }

        if($request->status_filter!=''){
            switch ($request->status_filter) {
                case 1:
                    $status = "Proceso";
                    break;
                case 2:
                    $status = "Pendiente";
                    break;
                case 3:
                    $status = "Entregado";
                    break;
                
                default:
                    $status = $request->status_filter;
                    break;
            }
        }
        
        $data=[
            'company' => $setting->company,
            'logo' => $logo,            
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'partner' => $partner,
            'customer' => $customer,
            'user' => $user,
            'status' => $status,
            'operations' => $operations            
        ];

        $pdf = PDF::loadView('reports/rpt_operations_'.strtolower(Auth::user()->role), $data)->setPaper('a4', 'landscape');
        
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
            $status=$request->status;
            if($operation->status==1){
                if($request->status==2){
                    $operation->s2_notes=$request->s2_notes;
                    $operation->status=2;                    
                }elseif($request->status==3){
                    $operation->s3_notes=($request->s3_notes)?$request->s3_notes:null;
                    $operation->status=3;
                }
            }elseif($operation->status==2){
                $operation->s3_notes=($request->s3_notes)?$request->s3_notes:null;
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
