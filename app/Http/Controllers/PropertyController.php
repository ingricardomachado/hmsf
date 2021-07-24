<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PropertyRequest;
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
use App\Exports\StatementExport;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Carbon\Carbon;

class PropertyController extends Controller
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
     * Display a listing of the property.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('properties.index');
    }

    public function datatable()
    {        

        if(session('role')=='OWN'){
            $properties=Auth::user()->properties()
                ->leftjoin('users', 'properties.user_id', '=', 'users.id')
                ->select(['properties.*', 'users.name as user', 'users.cell as cell']);
        }else{
            $properties=$this->condominium->properties()
                ->leftjoin('users', 'properties.user_id', '=', 'users.id')
                ->select(['properties.*', 'users.name as user', 'users.cell as cell']);    
        }
        
        return Datatables::of($properties)
            ->addColumn('action', function ($property) {
                    if(session('role')=='OWN'){
                        return "";
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalProperty('.$property->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="'.url('statement', Crypt::encrypt($property->id)).'" class="modal-class"><i class="fa fa-file-text-o"></i> Estado de Cuenta</a>
                                </li>

                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$property->id.'`, `'.$property->number.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('number', function ($property) {                    
                    return '<a href="'.url('statement', Crypt::encrypt($property->id)).'" class="modal-class" style="color:inherit"  title="Click para estado de cuenta"><b>'.$property->number.'</b></a>';
                })
            ->editColumn('due_debt', function ($property) {                    
                    return money_fmt($property->due_debt);
                })
            ->editColumn('debt', function ($property) {                    
                    return money_fmt($property->debt);
                })
            ->editColumn('total_debt', function ($property) {                    
                    return money_fmt($property->total_debt);
                })
            ->editColumn('status', function ($property) {                    
                    return $property->status_label;
                })
            ->rawColumns(['action', 'number', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified property.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $users=$this->condominium->users()->orderBy('name')->pluck('name','id');        
        if($id==0){
            $property = new Property();
        }else{
            $property = Property::find($id);
        }
        
        return view('properties.save')->with('property', $property)
                                ->with('users', $users);
    }

    /**
     * Store a newly created property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {
        try {
            $property = new Property();
            $property->number=$request->number;
            $property->condominium_id=$request->condominium_id;
            $property->user_id=($request->user)?$request->user:null;
            $property->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Propiedad registrada exitosamente',
                    'property' => $property->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyRequest $request, $id)
    {
        try {
            $property = Property::find($id);
            $property->number=$request->number;
            $property->user_id=($request->user)?$request->user:null;
            $property->save();            

            return response()->json([
                    'success' => true,
                    'message' => 'Propiedad actualizado exitosamente',
                    'property' => $property
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified property from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $property = Property::find($id);
            $property->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Propiedad eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rpt_properties()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'properties' => $this->condominium->properties()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_properties', $data);
        
        return $pdf->stream('Propiedads.pdf');

    }

    public function xls_properties(Request $request)
    {        
        $condominium=$this->condominium;
        return Excel::download(new PropertiesExport($condominium), 'Propiedades de '.$condominium->name.'.xlsx');        
    }

    public function taxes()
    {                
        $properties=$this->condominium->properties()->get();

        return view('properties.taxes')->with('properties', $properties);
    }

    public function update_taxes(Request $request)
    {                
        try {
            
            $array_properties=$request->array_properties;
            $array_taxes=$request->array_taxes;

            for ($i=0; $i < count($array_properties) ; $i++) { 
                $property= Property::find($array_properties[$i]);
                $property->tax=$array_taxes[$i];
                $property->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Alicuotas actualizadas exitosamente'
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
    public function statement($id)
    {
        $property=Property::findOrFail(Crypt::decrypt($id));
        $fees=$property->fees()->where('balance', '>',0)->get();
        
        return view('properties.statement')->with('property', $property)
                                    ->with('fees', $fees);
    }

    public function rpt_statement($id)
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $property=Property::find(Crypt::decrypt($id));
        $fees=$property->fees()->where('balance', '>',0)->get();

        $data=[
            'company' => $this->condominium->name,
            'logo' => $logo,            
            'property' => $property,
            'fees' => $fees
        ];

        $pdf = PDF::loadView('reports/rpt_statement', $data);
        
        return $pdf->stream('Estado de Cuenta '.$property->number.'.pdf');
    }

    public function xls_statement($id)
    {        
        $property=Property::find(Crypt::decrypt($id));
        $fees=$property->fees()->where('balance', '>',0)->get();

        return Excel::download(new StatementExport($property, $fees), 'Estado de Cuenta '.$property->number.'.xlsx');        
    }
}
