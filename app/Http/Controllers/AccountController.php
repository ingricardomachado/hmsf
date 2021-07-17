<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
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
//Export
use App\Exports\MovementsExport;
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
                                    <a href="'.url('accounts.statement',$account->id).'" name="href_cancel" class="modal-class"><i class="fa fa-file-text-o"></i> Estado de Cuenta</a>
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
                    $info=($account->type=='C')?'Caja':$account->bank.'<br><small>'.$account->number.'<br>'.$account->holder.'</small>';

                    return '<a href="'.url('accounts.statement', $account->id).'" class="modal-class" style="color:inherit"  title="Click para estado de cuenta"><b>'.$account->aliase.'</b><br>'.$info.'</a>';
                })
            ->editColumn('status', function ($account) {                    
                    return $account->status_label;
                })
            ->editColumn('initial_balance', function ($account) {                    
                    return money_fmt($account->initial_balance).'<br><small>'.$account->date_initial_balance->format('d/m/Y').'</small>';
                })
            ->editColumn('credits', function ($account) {                    
                    return money_fmt($account->credits);
                })
            ->editColumn('debits', function ($account) {                    
                    return money_fmt($account->debits);
                })
            ->editColumn('balance', function ($account) {                    
                    return money_fmt($account->balance);
                })
            ->rawColumns(['action', 'aliase', 'initial_balance', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {            
        try {
            
            $account = Account::findOrFail($id);
        
            return response()->json([
                'account' => $account,
            ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }            
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

    /**
     * Display the specified account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statement($id)
    {
        $account=Account::findOrFail($id);
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        
        return view('accounts.statement')->with('account', $account)
                                    ->with('start', $start->format('d/m/Y'))
                                    ->with('end', $end->format('d/m/Y'));
    }


    public function get_movements($account_id, $start, $end){
        
        $movements = Movement::where('account_id', $account_id)
                        ->whereDate('date', '>=', $start)
                        ->whereDate('date', '<=', $end)
                        ->orderBy('date')->orderBy('id')->get();        

        return $movements;
    }
    
    public function movements(Request $request)
    {        
        $account=Account::find($request->account);
        $start=Carbon::createFromFormat('d/m/Y', $request->start);
        $end=Carbon::createFromFormat('d/m/Y', $request->end);
        $movements=$this->get_movements($account->id, $start, $end);

        return view('accounts.movements')->with('account', $account)
                    ->with('start', $start)
                    ->with('end', $end)
                    ->with('movements', $movements);
    }

    public function xls_movements(Request $request)
    {        
        $account=Account::find($request->account);
        $start=Carbon::createFromFormat('d/m/Y', $request->start);
        $end=Carbon::createFromFormat('d/m/Y', $request->end);
        $movements=$this->get_movements($account->id, $start, $end);

        return Excel::download(new MovementsExport($start, $account, $movements), 'Estado de Cuenta '.$account->aliase.'.xlsx');        
    }

    public function rpt_movements(Request $request)
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $account=Account::find($request->account);
        $start=Carbon::createFromFormat('d/m/Y', $request->start);
        $end=Carbon::createFromFormat('d/m/Y', $request->end);
        $movements=$this->get_movements($account->id, $start, $end);

        $data=[
            'company' => $this->condominium->name,
            'logo' => $logo,            
            'account' => $account,
            'start' => $start,
            'end' => $end,
            'movements' => $movements
        ];

        $pdf = PDF::loadView('reports/rpt_movements', $data);
        
        return $pdf->stream('Estado de Cuenta '.$account->aliase.'.pdf');
    
    }

}
