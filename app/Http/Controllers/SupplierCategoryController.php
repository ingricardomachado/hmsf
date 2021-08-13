<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SupplierCategoryRequest;
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

class SupplierCategoryController extends Controller
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
     * Display a listing of the supplier_category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('supplier_categories.index');
    }

    public function datatable()
    {        

        $supplier_categories = $this->condominium->supplier_categories();        
        
        return Datatables::of($supplier_categories)
            ->addColumn('action', function ($supplier_category) {
                $supplier_category_id = Crypt::encrypt($supplier_category->id);
                $url_edit = route('supplier_categories.edit', $supplier_category_id);
                    if($supplier_category->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalSupplierCategory('.$supplier_category->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$supplier_category->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$supplier_category->id.'`, `'.$supplier_category->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$supplier_category->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('name', function ($supplier_category) {                    
                    return '<a href="#"  onclick="showModalSupplierCategory('.$supplier_category->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$supplier_category->name.'</a>';
                })
            ->editColumn('status', function ($supplier_category) {                    
                    return $supplier_category->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified supplier_category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $supplier_category = new SupplierCategory();
        }else{
            $supplier_category = SupplierCategory::find($id);
        }
        
        return view('supplier_categories.save')->with('supplier_category', $supplier_category);
    }

    /**
     * Store a newly created supplier_category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierCategoryRequest $request)
    {
        try {
            $supplier_category = new SupplierCategory();
            $supplier_category->condominium_id=$this->condominium->id;
            $supplier_category->name=$request->name;
            $supplier_category->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Categoría registrada exitosamente',
                    'supplier_category' => $supplier_category->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified supplier_category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierCategoryRequest $request, $id)
    {
        try {
            $supplier_category = SupplierCategory::find($id);
            $property=Property::find($request->property);
            $supplier_category->property_id=$property->id;
            $supplier_category->plate=strtoupper($request->plate);
            $supplier_category->make=$request->make;
            $supplier_category->model=$request->model;
            $supplier_category->color=$request->color;
            $supplier_category->year=$request->year;
            $supplier_category->notes=$request->notes;
            $supplier_category->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente',
                    'supplier_category' => $supplier_category
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified supplier_category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $supplier_category = SupplierCategory::find($id);
            $supplier_category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
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
            $supplier_category = SupplierCategory::find($id);
            ($supplier_category->active)?$supplier_category->active=false:$supplier_category->active=true;
            $supplier_category->save();

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
    
    public function rpt_supplier_categories()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'supplier_categories' => $this->condominium->supplier_categories()->orderBy('name')->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_supplier_categories', $data);
        
        return $pdf->stream('Categorías.pdf');

    }
}
