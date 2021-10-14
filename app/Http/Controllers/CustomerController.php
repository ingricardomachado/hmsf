<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Partner;
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
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class CustomerController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $partners=Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->where('active',true)
                        ->orderBy('full_name')->pluck('full_name','partners.id');        
        
        return view('customers.index')->with('partners', $partners);
    }

    public function datatable(Request $request)
    {        
        $partner_filter=$request->partner_filter;

        if($partner_filter!=''){
            $customers = Customer::where('partner_id', $partner_filter)->orderBy('name');
        }else{
            $customers = Customer::orderBy('name');
        }

        
        return Datatables::of($customers)
            ->addColumn('action', function ($customer) {
                    if($customer->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="" class="modal-class" onclick="showModalCustomer('.$customer->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$customer->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$customer->id.'`, `'.$customer->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$customer->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('name', function ($customer) {                    
                    return '<a href="#"  onclick="showModalCustomer('.$customer->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$customer->name.'</b><br><span class="text-muted">'.$customer->code.'</span><br><small><i>'.$customer->email.'</i></small></a>';
                })
            ->addColumn('partner', function ($customer) {                    
                    return $customer->partner->user->full_name;
                })
            ->editColumn('tax', function ($customer) {                    
                    return $customer->tax.'%';
                })
            ->addColumn('operations', function ($customer) {                    
                    return $customer->operations()->count();
                })
            ->editColumn('status', function ($customer) {                    
                    return $customer->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $partners=Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->where('active',true)
                        ->orderBy('full_name')->pluck('full_name','partners.id');        
        
        if($id==0){
            $customer = new Customer();
        }else{
            $customer = Customer::find($id);
        }
        
        return view('customers.save')->with('customer', $customer)
                        ->with('partners', $partners);
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        try {
            $customer = new Customer();
            $customer->number=Customer::max('number')+1;
            $customer->partner_id=$request->partner;
            $customer->name=$request->name;
            $customer->email=$request->email;
            $customer->cell=$request->cell;
            $customer->tax=$request->tax;
            $customer->contract=($request->has_contract)?$request->contract:null;
            $customer->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Cliente registrado exitosamente',
                    'customer' => $customer->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
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
            $customer = Customer::findOrFail($id);
            
            return response()->json([
                    'success' => true,
                    'customer' => $customer
                ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);            
        }
    }
   
   /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->partner_id=$request->partner;
            $customer->name=$request->name;
            $customer->email=$request->email;
            $customer->cell=$request->cell;
            $customer->tax=$request->tax;
            $customer->contract=($request->has_contract)?$request->contract:null;
            $customer->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Cliente actualizado exitosamente',
                    'customer' => $customer
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
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
            $customer = Customer::findOrFail($id);
            ($customer->active)?$customer->active=false:$customer->active=true;
            $customer->save();

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
    
    public function rpt_customers()
    {        
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';

        $customers=Customer::orderBy('name')->get();

        $data=[
            'company' => $setting->company,
            'customers' => $customers,
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_customers', $data);
        
        return $pdf->stream('Clientes.pdf');

    }
}
