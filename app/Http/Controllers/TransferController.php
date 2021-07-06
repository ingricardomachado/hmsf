<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\TransferRequest;
use App\Models\Transfer;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Movement;
use App\Models\Account;
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

class TransferController extends Controller
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
     * Display a listing of the transfer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('transfers.index');
    }

    public function datatable()
    {        
        /*Se construye asi para que funcione el search de Yajra*/
        $transfers = $this->condominium->transfers();
        
        return Datatables::of($transfers)
            ->addColumn('action', function ($transfer) {
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$transfer->id.'`, `'.$transfer->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('transfer', function ($transfer) {                    
                    return '<a href="#"  onclick="showModalTransfer('.$transfer->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$transfer->concept.'</a>';
                })
            ->editColumn('date', function ($transfer) {                    
                    return $transfer->date->format('d/m/Y');
                })
            ->editColumn('transfer', function ($transfer) {                    
                    return '<b>Origen:</b> '.$transfer->from_account->aliase.'<br> <b>Destino:</b> '.$transfer->to_account->aliase;
                })
            ->editColumn('amount', function ($transfer) {                    
                    return money_fmt($transfer->amount);
                })
            ->addColumn('file', function ($transfer) {
                    if($transfer->file_name){                    
                        $ext=$transfer->file_type;
                        if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                            $url_show_file = url('transfer_image', $transfer->id);
                            return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$transfer->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
                        }else{
                            $url_download_file = route('transfers.download', $transfer->id);
                            return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$transfer->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
                        }
                    }
                })
            ->rawColumns(['action', 'transfer', 'account', 'file'])
            ->make(true);
    }
    
    /**
     * Display the specified transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {        
        $accounts=$this->condominium->accounts()->orderBy('aliase')->pluck('aliase','id');

        $today=Carbon::now();

        if($id==0){
            $transfer = new Transfer();
        }else{
            $transfer = Transfer::find($id);
        }
        
        return view('transfers.save')->with('transfer', $transfer)
                                ->with('today', $today)
                                ->with('accounts', $accounts);
    }

    /**
     * Store a newly created transfer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransferRequest $request)
    {
        try {
            $transfer = new Transfer();
            $transfer->condominium_id=$request->condominium_id;
            $transfer->from_account_id=$request->from_account;
            $transfer->to_account_id=$request->to_account;
            $transfer->payment_method=$request->payment_method;
            $transfer->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $transfer->amount=$request->amount;
            $transfer->concept=$request->concept;
            $transfer->reference=$request->reference;
            $file = $request->file;
            if (File::exists($file)){
                $transfer->file_name = $file->getClientOriginalName();
                $transfer->file_type = $file->getClientOriginalExtension();
                $transfer->file_size = $file->getSize();
                $transfer->file=$this->upload_file($transfer->condominium_id.'/transfers/', $file);
            }
            $transfer->save();
            //se registra el movimiento de debito
            Movement::create([
                'type' => 'D',
                'account_id' => $transfer->from_account_id,
                'transfer_id' => $transfer->id,
                'date' => $transfer->date,
                'amount' => $transfer->amount
            ]);
            //se actualizan los saldos de la cuenta origen
            $transfer->from_account->update_balance();                       
            
            //se registra el movimiento de credito
            Movement::create([
                'type' => 'C',
                'account_id' => $transfer->to_account_id,
                'transfer_id' => $transfer->id,
                'date' => $transfer->date,
                'amount' => $transfer->amount
            ]);
            //se actualizan los saldos de la cuenta destino
            $transfer->to_account->update_balance();                       
            
            return response()->json([
                    'success' => true,
                    'message' => 'Transferencia registrada exitosamente',
                    'transfer' => $transfer->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified transfer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransferRequest $request, $id)
    {
        try {
                //            
            return response()->json([
                    'success' => true,
                    'message' => 'Transferencia actualizada exitosamente',
                    'transfer' => $transfer->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified transfer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transfer = Transfer::find($id);
            $from_account=Account::find($transfer->from_account_id);
            $to_account=Account::find($transfer->to_account_id);
            
            Storage::delete($transfer->condominium_id.'/transfers/'.$transfer->file);
            Storage::delete($transfer->condominium_id.'/transfers/thumbs/'.$transfer->file);
            $transfer->delete();
            
            //se actualizan los saldos de las cuentas afectadas
            $from_account->update_balance();
            $to_account->update_balance();
            
            return response()->json([
                'success' => true,
                'message' => 'Transferencia eliminada exitosamente'
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
        $transfer = Document::find($id);
        return response()->download(storage_path('app/'.$transfer->condominium_id.'/transfers/'.$transfer->file), $transfer->file_name);
    }
    
}
