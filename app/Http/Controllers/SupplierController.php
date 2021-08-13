<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierCategory;
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
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class SupplierController extends Controller
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
     * Display a listing of the supplier.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $supplier_categories=$this->condominium->supplier_categories()->orderBy('name')->pluck('name','id');
        
        return view('suppliers.index')->with('supplier_categories', $supplier_categories);
    }

    public function datatable(Request $request)
    {        
        $supplier_category_filter=$request->supplier_category_filter;

        if($supplier_category_filter!=''){
            $suppliers = $this->condominium->suppliers()->where('supplier_category_id', $supplier_category_filter);
        }else{
            $suppliers = $this->condominium->suppliers();
        }
                
        return Datatables::of($suppliers)
            ->addColumn('action', function ($supplier) {
                $supplier_id = Crypt::encrypt($supplier->id);
                $url_edit = route('suppliers.edit', $supplier_id);
                    if($supplier->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalSupplier('.$supplier->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$supplier->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$supplier->id.'`, `'.$supplier->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$supplier->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('name', function ($supplier) {                    
                    return '<a href="#"  onclick="showModalSupplier('.$supplier->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$supplier->name.'</b><br><small><i>'.$supplier->supplier_category->name.'</small></i></a>';
                })
            ->editColumn('phone', function ($supplier) {                    
                    return $supplier->phone.'<br><small><i>'.$supplier->contact.'</i><small>';
                })
            ->editColumn('status', function ($supplier) {                    
                    return $supplier->status_label;
                })
            ->rawColumns(['action', 'name', 'phone', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified supplier.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $supplier_categories=$this->condominium->supplier_categories()->orderBy('name')->pluck('name','id');

        if($id==0){
            $supplier = new Supplier();
        }else{
            $supplier = Supplier::find($id);
        }
        
        return view('suppliers.save')->with('supplier', $supplier)
                            ->with('supplier_categories', $supplier_categories);
    }

    /**
     * Store a newly created supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        try {
            $supplier = new Supplier();
            $supplier_category=SupplierCategory::find($request->supplier_category);
            $supplier->condominium_id=$supplier_category->condominium_id;
            $supplier->supplier_category_id=$request->supplier_category;
            $supplier->name=$request->name;
            $supplier->NIT=$request->NIT;
            $supplier->address=$request->address;
            $supplier->contact=$request->contact;
            $supplier->phone=$request->phone;
            $supplier->email=$request->email;
            $supplier->url=($request->url)?$request->url:null;
            $supplier->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Proveedor registrado exitosamente',
                    'supplier' => $supplier->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->supplier_category_id=$request->supplier_category;
            $supplier->name=$request->name;
            $supplier->NIT=$request->NIT;
            $supplier->address=$request->address;
            $supplier->contact=$request->contact;
            $supplier->phone=$request->phone;
            $supplier->email=$request->email;
            $supplier->url=($request->url)?$request->url:null;
            $supplier->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Proveedor actualizado exitosamente',
                    'supplier' => $supplier
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified supplier from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
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
            $supplier = Supplier::find($id);
            ($supplier->active)?$supplier->active=false:$supplier->active=true;
            $supplier->save();

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
    
    public function rpt_suppliers()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'suppliers' => $this->condominium->suppliers()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_suppliers', $data);
        
        return $pdf->stream('Proveedors.pdf');

    }
}
