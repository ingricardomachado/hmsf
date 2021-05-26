<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use App\Exports\PropertiesExport;
use Carbon\Carbon;
use Image;
use File;
use DB;
use PDF;
use Auth;

class AccountController extends Controller
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
     * Display a listing of the account.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('accounts.index');
    }

    public function datatable()
    {        

        $accounts = $this->condominium->accounts();        
        
        return Datatables::of($accounts)
            ->addColumn('action', function ($account) {
                    if($account->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalAccount('.$account->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$account->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$account->id.'`, `'.$account->aliase.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$account->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('aliase', function ($account) {                    
                    return '<a href="#"  onclick="showModalAccount('.$account->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$account->aliase.'</b><br><small>'.$account->date_initial_balance->format('d/m/Y').'<br>'.$account->type_description.'</small></a>';
                })
            ->editColumn('bank', function ($account) {                    
                    return $account->bank.'<br><small>'.$account->number.'<br>'.$account->holder.'</small>';
                })
            ->editColumn('status', function ($account) {                    
                    return $account->status_label;
                })
            ->editColumn('balance', function ($account) {                    
                    return money_fmt($account->balance);
                })
            ->rawColumns(['action', 'aliase', 'bank', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');

        if($id==0){
            $account = new Account();
        }else{
            $account = Account::find($id);
        }
        
        return view('accounts.save')->with('account', $account)
                                ->with('properties', $properties);
    }

    /**
     * Store a newly created account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        try {
            $account = new Account();
            $account->condominium_id=$request->condominium_id;
            $account->aliase= $request->aliase;
            $account->type=$request->type;
            if($account->type=='B'){
                $account->number= $request->number;
                $account->bank= strtoupper($request->bank);
                $account->holder= $request->holder;        
                $account->NIT= $request->NIT;
                $account->email= $request->email;
            }
            $account->date_initial_balance=Carbon::createFromFormat('d/m/Y', $request->date_initial_balance);
            $account->initial_balance=$request->initial_balance;
            $account->balance=$request->initial_balance;
            $account->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Cuenta registrado exitosamente',
                    'account' => $account->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, $id)
    {
        try {
            $account = Account::find($id);
            $account->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Cuenta actualizado exitosamente',
                    'account' => $account
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $account = Account::find($id);
            $account->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cuenta eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function status($id)
    {
        try {
            $account = Account::find($id);
            ($account->active)?$account->active=false:$account->active=true;
            $account->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Estado cambiado exitosamente',
                ], 200);                        

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }
    }
    
    public function rpt_accounts()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'accounts' => $this->condominium->accounts()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_accounts', $data);
        
        return $pdf->stream('Cuentas.pdf');

    }
}
