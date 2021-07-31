<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VisitTypeRequest;
use App\Models\VisitType;
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

class VisitTypeController extends Controller
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
     * Display a listing of the visit_type.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('visit_types.index');
    }

    public function datatable()
    {        
        $visit_types=$this->condominium->visit_types();
        
        return Datatables::of($visit_types)
            ->addColumn('action', function ($visit_type) {
                if($visit_type->condominium_id){
                    if($visit_type->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalVisitType('.$visit_type->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$visit_type->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$visit_type->id.'`, `'.$visit_type->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$visit_type->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }
                }else{
                    return "";
                }    
                })           
            ->editColumn('name', function ($visit_type) {                    
                    return '<a href="#"  onclick="showModalVisitType('.$visit_type->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$visit_type->name.'</a>';
                })
            ->editColumn('status', function ($visit_type) {                    
                    return $visit_type->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified visit_type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $visit_type = new VisitType();
        }else{
            $visit_type = VisitType::find($id);
        }
        
        return view('visit_types.save')->with('visit_type', $visit_type);
    }

    /**
     * Store a newly created visit_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisitTypeRequest $request)
    {
        try {
            $visit_type = new VisitType();
            $visit_type->condominium_id=$request->condominium_id;
            $visit_type->name=$request->name;
            $visit_type->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de visita registrado exitosamente',
                    'visit_type' => $visit_type->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified visit_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VisitTypeRequest $request, $id)
    {       
        try {
            $visit_type = VisitType::find($id);
            $visit_type->name=$request->name;
            $visit_type->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de visita actualizado exitosamente',
                    'visit_type' => $visit_type
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified visit_type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $visit_type = VisitType::find($id);
            $visit_type->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de visita eliminado exitosamente'
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
            $visit_type = VisitType::find($id);
            ($visit_type->active)?$visit_type->active=false:$visit_type->active=true;
            $visit_type->save();

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
    
    public function rpt_visit_types()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'visit_types' => VisitType::where('condominium_id', $this->condominium->id)->orWhereNull('condominium_id')->orderBy('name')->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_visit_types', $data);
        
        return $pdf->stream('Tipos de Ingresos.pdf');

    }
}
