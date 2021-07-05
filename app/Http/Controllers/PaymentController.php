<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Fee;
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
use Mail;
use App\Mail\PaymentConfirm;

class PaymentController extends Controller
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
     * Display a listing of the payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     })->pluck('name','id');
        
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        
        return view('payments.index')->with('properties', $properties)
                            ->with('income_types', $income_types);
    }

    public function datatable(Request $request)
    {        
        $property_filter=$request->property_filter;
        $status_filter=$request->status_filter;

        if($property_filter!=''){
            if($status_filter!=''){
                $payments = $this->condominium->payments()
                                ->where('property_id', $property_filter)
                                ->where('status', $status_filter);
            }else{
                $payments = $this->condominium->payments()
                                ->where('property_id', $property_filter);
            }
        }else{
            if($status_filter!=''){
                $payments = $this->condominium->payments()
                                ->where('status', $status_filter);
            }else{
                $payments = $this->condominium->payments();
            }
        }
 
        return Datatables::of($payments)
            ->addColumn('action', function ($payment) {
                $opt_rpt=($payment->status=='A')?
                    '<li>
                        <a href="'.route('payments.rpt_payment', $payment->id).'" target="_blank" name="href_rpt_invoice" class="modal-class"><i class="fa fa-print"></i> Imprimir recibo</a>
                    </li>':'';
                if($payment->status=='P'){
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" class="modal-class" onclick="showModalPaymentInfo('.$payment->id.')"><i class="fa fa-laptop"></i> Ver Detalle</a>
                            </li>
                            <li>
                                <a href="#" class="modal-class" onclick="showModalConfirmPayment('.$payment->id.')"><i class="fa fa-check-square-o"></i> Confirmar pago</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$payment->id.'`, `'.$payment->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                }else{
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" class="modal-class" onclick="showModalPaymentInfo('.$payment->id.')"><i class="fa fa-laptop"></i> Ver Detalle</a>
                            </li>
                            '.$opt_rpt.'
                        </ul>
                    </div>';
                }    
                })           
            ->addColumn('property', function ($payment) {                    
                    return $payment->property->number.'<br><small><i>'.(($payment->property->user_id)?$payment->property->user->name:'').'</i></small>';
                })
            ->editColumn('payment', function ($payment) {                    
                    if($payment->reference){
                        $label='<b>'.$payment->concept.'</b><br>'.$payment->account->aliase.' REF '.$payment->reference.'<br><small><i>'.$payment->payment_method_description.'</i></small>';
                    }else{
                        $label='<b>'.$payment->concept.'</b><br>'.$payment->account->aliase.'<br><small><i>'.$payment->payment_method_description.'</i></small>';
                    }
                    if($payment->status=='P'){
                        return '<a href="#"  onclick="showModalConfirmPayment('.$payment->id.')" class="modal-class" style="color:inherit"  title="Click para confirmar">'.$label;
                    }else{
                        return '<a href="#"  onclick="showModalPaymentInfo('.$payment->id.')" class="modal-class" style="color:inherit"  title="Click para ver detalle">'.$label;
                    }
                })
            ->editColumn('date', function ($payment) {                    
                    return $payment->date->format('d/m/Y');
                })
            ->editColumn('amount', function ($payment) {                    
                    return money_fmt($payment->amount);
                })
            ->addColumn('file', function ($payment) {
                    if($payment->file_name){                    
                        $ext=$payment->file_type;
                        if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                            $url_show_file = url('payment_image', $payment->id);
                            return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$payment->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
                        }else{
                            $url_download_file = route('payments.download', $payment->id);
                            return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$payment->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
                        }
                    }
                })
            ->editColumn('status', function ($payment) {                    
                    return $payment->status_label;
                })
            ->rawColumns(['action', 'property', 'payment', 'account', 'status', 'file'])
            ->make(true);
    }
    
    public function info($id){

        $payment=Payment::findOrFail($id);

        return view('payments.info')->with('payment', $payment);
    }

    /**
     * Display the specified payment.
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
        
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        $accounts=$this->condominium->accounts()->orderBy('aliase')->pluck('aliase','id');

        $today=Carbon::now();

        if($id==0){
            $payment = new Payment();

            return view('payments.create')->with('payment', $payment)
                        ->with('today', $today)
                        ->with('properties', $properties)
                        ->with('accounts', $accounts);
        }else{
            $payment = Payment::find($id);
            
            return view('payments.save')->with('payment', $payment)
                        ->with('today', $today)
                        ->with('properties', $properties)
                        ->with('accounts', $accounts);

        }
        
    }

    public function load_pending_fees($id){
        
        $property=Property::find($id);
        //una cuota esta bloqueada cuando se le hace un pago o abono y este está pendiente por confirmar, una vez confirmado se desbloquea
        $pending_fees=$property->fees()
                        ->where('balance','>',0)
                        ->where('locked', false)->get();
        
        return view('payments.pending_fees')->with('property', $property)
                        ->with('pending_fees', $pending_fees);
    }
    /**
     * Store a newly created payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        try {
            $property=Property::find($request->property);
            $payment = new Payment();
            $payment->condominium_id=$property->condominium_id;
            $payment->property_id=$property->id;
            $payment->account_id=$request->account;
            $payment->payment_method=$request->payment_method;
            $payment->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $payment->concept=$request->concept;
            $payment->reference=$request->reference;
            $file = $request->file;
            if (File::exists($file)){
                $payment->file_name = $file->getClientOriginalName();
                $payment->file_type = $file->getClientOriginalExtension();
                $payment->file_size = $file->getSize();
                $payment->file=$this->upload_file($payment->condominium_id.'/payments/', $file);
            }
            $payment->save();
            $array_fees=json_decode($request->array_fees);
            $array_amounts=json_decode($request->array_amounts);
            for ($i=0; $i < sizeof($array_fees); $i++) {
                $payment->fees()->attach([
                    $array_fees[$i] => [
                        'amount' => $array_amounts[$i]
                    ]]);
            }
            Fee::whereIn('id', $array_fees)->update(array('locked' => true));
            $payment->amount=$payment->fees->sum('pivot.amount');
            $payment->save();
            
            /*if($request->notification && $payment->supplier_id && $payment->supplier->email){
                Mail::to($payment->supplier->email)->send(new PaymentNotification($payment));
            }*/
            
            return response()->json([
                    'success' => true,
                    'message' => 'Pago registrado exitosamente',
                    'payment' => $payment->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentRequest $request, $id)
    {
        try {
            $payment = Payment::find($id);
            $payment->condominium_id=$request->condominium_id;
            $payment->supplier_id=($request->supplier)?$request->supplier:null;
            $payment->project_id=($request->project)?$request->project:null;
            $payment->payment_type_id=$request->payment_type;
            $payment->account_id=$request->account;
            $payment->payment_method=$request->payment_method;
            $payment->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $payment->amount=$request->amount;
            $payment->concept=$request->concept;
            $payment->reference=$request->reference;
            $payment->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($payment->file){
                    Storage::delete($payment->condominium_id.'/payments/'.$payment->file);
                    Storage::delete($payment->condominium_id.'/payments/thumbs/'.$payment->file);
                }
                $payment->file_name = $file->getClientOriginalName();
                $payment->file_type = $file->getClientOriginalExtension();
                $payment->file_size = $file->getSize();
                $payment->file=$this->upload_file($payment->condominium_id.'/payments/', $file);
            }
            $payment->save();
            $payment->account->update_balance();
            
            if($request->notification && $payment->supplier_id && $payment->supplier->email){
                Mail::to($payment->supplier->email)->send(new PaymentNotification($payment));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Egreso actualizado exitosamente',
                    'payment' => $payment->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified payment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::find($id);
            Storage::delete($payment->condominium_id.'/payments/'.$payment->file);
            Storage::delete($payment->condominium_id.'/payments/thumbs/'.$payment->file);

            $payment->delete();
            $payment->account->update_balance();
            
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

    public function load_confirm($id)
    {
        $payment=Payment::findOrFail($id);
        
        return view('payments.confirm')->with('payment', $payment);
    }
    
   /**
     * Update the specified reservation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            if($request->resp=='A'){
                /*
                    1. Se pasa el pago a aprobado
                    2. Se actualiza el saldo de cada cuota pagada
                    3. Se actualiza el saldo de la cuenta afectada por el pago
                    4. Se registra el movimiento en la cuenta
                    4. Se envia la notificacion
                */
                $payment->status='A';
                $payment->observations=$request->observations;
                $payment->save();
                foreach ($payment->fees()->get() as $fee) {
                    $fee->update_balance();
                    $fee->locked=false;
                    $fee->save();
                }
                //se registra el movimiento
                Movement::create([
                    'type' => 'C',
                    'account_id' => $payment->account_id,
                    'payment_id' => $payment->id,
                    'date' => $payment->date,
                    'amount' => $payment->amount
                ]);
                //se actualizan los saldos de la cuenta                
                $payment->account->update_balance();
            }else{
                /*
                    1. Se pasa el pago a rechazado
                    2. Se envia la notificacion
                */
                $payment->status='R';
                $payment->observations=$request->observations;
                $payment->save();
            }
            (false && $payment->property->user_id)?Mail::to($payment->property->user->email)->send(new PaymentConfirm($payment)):'';

            return response()->json([
                    'success' => true,
                    'message' => 'Pago confirmado exitosamente',
                    'payment' => $payment->toArray()
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
        $payment = Document::find($id);
        return response()->download(storage_path('app/'.$payment->condominium_id.'/payments/'.$payment->file), $payment->file_name);
    }
    
}
