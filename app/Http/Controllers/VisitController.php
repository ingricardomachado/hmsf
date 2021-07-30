<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VisitRequest;
use App\Models\Account;
use App\Models\Visit;
use App\Models\VisitType;
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

class VisitController extends Controller
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
     * Display a listing of the visit.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('visits.index');
    }

    public function datatable()
    {        
        $visits = $this->condominium->visits();
        
        return Datatables::of($visits)
            ->addColumn('action', function ($visit) {
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalVisit('.$visit->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$visit->id.'`, `'.$visit->concept.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('visit', function ($visit) {                    
                    return '<a href="#"  onclick="showModalVisit('.$visit->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$visit->notes.'<br><small><i>'.$visit->visit_type->name.'</i></small></a>';
                })
            ->addColumn('file', function ($visit) {
                    return $visit->download_file;
                })
            ->rawColumns(['action', 'visit', 'file'])
            ->make(true);
    }
    
    /**
     * Display the specified visit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        
        $visit_types=$this->condominium->visit_types()->orderBy('name')->pluck('name','id');
        $today=Carbon::now();

        if($id==0){
            $visit = new Visit();
        }else{
            $visit = Visit::find($id);
        }
        
        return view('visits.save')->with('visit', $visit)
                                ->with('today', $today)
                                ->with('visit_types', $visit_types);
    }

    /**
     * Store a newly created visit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisitRequest $request)
    {
        try {
            $visit = new Visit();
            $visit->condominium_id=$request->condominium_id;
            $visit->visit_type_id=$request->visit_type;
            $visit->property_id=$request->property_id;
            $visit->user_id=$request->user_id;
            $visit->checkin=Carbon::createFromFormat('d/m/Y H:i', $request->checkin);
            $visit->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                $visit->file_name = $file->getClientOriginalName();
                $visit->file_type = $file->getClientOriginalExtension();
                $visit->file_size = $file->getSize();
                $visit->file=$this->upload_file($visit->condominium_id.'/visits/', $file);
            }
            $visit->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Visita registrada exitosamente',
                    'visit' => $visit->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified visit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VisitRequest $request, $id)
    {
        try {
            $visit = Visit::find($id);
            $visit->visit_type_id=$request->visit_type;
            $visit->property_id=$request->property_id;
            $visit->checkin=Carbon::createFromFormat('d/m/Y H:i', $request->checkin);
            $visit->notes=$request->notes;
            $file = $request->file;
            if (File::exists($file)){
                if($visit->file){
                    Storage::delete($visit->condominium_id.'/visits/'.$visit->file);
                    Storage::delete($visit->condominium_id.'/visits/thumbs/'.$visit->file);
                }
                $visit->file_name = $file->getClientOriginalName();
                $visit->file_type = $file->getClientOriginalExtension();
                $visit->file_size = $file->getSize();
                $visit->file=$this->upload_file($visit->condominium_id.'/visits/', $file);
            }
            $visit->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Visita actualizada exitosamente',
                    'visit' => $visit->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified visit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $visit = Visit::find($id);
            if($visit->file){
                Storage::delete($visit->condominium_id.'/visits/'.$visit->file);
                Storage::delete($visit->condominium_id.'/visits/thumbs/'.$visit->file);
            }
            $visit->delete();
                        
            return response()->json([
                'success' => true,
                'message' => 'Visita eliminada exitosamente'
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
        $visit = Document::find($id);
        
        return Storage::download($visit->condominium_id.'/visits/'.$visit->file, $visit->file_name);
    }
    
}
