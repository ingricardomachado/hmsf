<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\IncomeTypeRequest;
use App\Models\IncomeType;
use App\Models\Property;
use App\Models\Setting;
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
use Image;
use File;
use DB;
use PDF;
use Auth;

class IncomeTypeController extends Controller
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
     * Display a listing of the income_type.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('income_types.index');
    }

    public function datatable()
    {        
        /*Se construye asi para que funcione el search de Yajra*/
        $income_types = IncomeType::where(function($query) {
                        $query->where('condominium_id', $this->condominium->id);
                        $query->orWhere(function($query_two) {
                           $query_two->whereNull('condominium_id');
                           });
                     });
        
        return Datatables::of($income_types)
            ->addColumn('action', function ($income_type) {
                if($income_type->condominium_id){
                    if($income_type->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalIncomeType('.$income_type->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$income_type->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$income_type->id.'`, `'.$income_type->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$income_type->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }
                }else{
                    return "";
                }    
                })           
            ->editColumn('name', function ($income_type) {                    
                    return '<a href="#"  onclick="showModalIncomeType('.$income_type->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$income_type->name.'</a>';
                })
            ->editColumn('status', function ($income_type) {                    
                    return $income_type->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified income_type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $income_type = new IncomeType();
        }else{
            $income_type = IncomeType::find($id);
        }
        
        return view('income_types.save')->with('income_type', $income_type);
    }

    /**
     * Store a newly created income_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IncomeTypeRequest $request)
    {
        /*Esta validacion es para comparar contra los predefnidos*/

        $msgs = ['name.unique' => 'El tipo de ingreso ya existe'];        
        $rules['name'] = 'required|unique:income_types,name,NULL,id,condominium_id,NULL';
        
        $validatedData = $request->validate($rules, $msgs);

        try {
            $income_type = new IncomeType();
            $income_type->condominium_id=$request->condominium_id;
            $income_type->name=$request->name;
            $income_type->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de ingreso registrado exitosamente',
                    'income_type' => $income_type->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified income_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IncomeTypeRequest $request, $id)
    {
        /*Esta validacion es para comparar contra los predefnidos*/

        $msgs = ['name.unique' => 'El tipo de ingreso ya existe'];
        $rules['name'] = 'required|unique:income_types,name,'.$id.',id,condominium_id,NULL';

        $validatedData = $request->validate($rules, $msgs);
        
        try {
            $income_type = IncomeType::find($id);
            $income_type->name=$request->name;
            $income_type->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de ingreso actualizado exitosamente',
                    'income_type' => $income_type
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified income_type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $income_type = IncomeType::find($id);
            $income_type->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de ingreso eliminado exitosamente'
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
            $income_type = IncomeType::find($id);
            ($income_type->active)?$income_type->active=false:$income_type->active=true;
            $income_type->save();

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
    
    public function rpt_income_types()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'income_types' => IncomeType::where('condominium_id', $this->condominium->id)->orWhereNull('condominium_id')->orderBy('name')->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_income_types', $data);
        
        return $pdf->stream('Tipos de Ingresos.pdf');

    }
}
