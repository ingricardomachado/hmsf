<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VisitRequest;
use App\Models\Account;
use App\Models\Visit;
use App\Models\VisitType;
use App\Models\Property;
use App\Models\Visitor;
use App\Models\VisitingCar;
use App\User;
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
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');        
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        $users=$this->condominium->users()->where('role', 'WAM')->orderBy('name')->pluck('name','id');

        return view('visits.index')->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('users', $users)
                        ->with('properties', $properties);
    }

    public function datatable(Request $request)
    {        
        $visits = $this->get_visits_collection($request);
        
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
            ->editColumn('visitor', function ($visit) {                    
                    if($visit->visiting_car_id){
                        return '<b>'.$visit->visitor->name.'</b><br>'.$visit->visitor->NIT.'<br>'.$visit->visiting_car->plate.' '.$visit->visiting_car->make.' '.$visit->visiting_car->model.'<br><span class="text-muted"><small>'.Carbon::parse($visit->checkin)->isoFormat('LLLL').'</small></span>';
                    }else{
                        return '<b>'.$visit->visitor->name.'</b><br>'.$visit->visitor->NIT;
                    }
                })
            ->editColumn('visit', function ($visit) {                    
                    return '<a href="#"  onclick="showModalVisit('.$visit->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$visit->notes.'<br><small>'.$visit->visit_type->name.'</small></a>';
                })
            ->editColumn('property', function ($visit) {                    
                    if($visit->property->user_id){
                        return '<b>'.$visit->property->number.'</b> '.(($visit->property->user_id)?$visit->property->user->name:'').'<br>'.$visit->property->user->cell;
                    }else{
                        return '<b>'.$visit->property->number.'</b>';
                    }
                })
            ->editColumn('user', function ($visit) {                    
                    return $visit->user->name;
                })
            ->addColumn('file', function ($visit) {
                    return $visit->download_file;
                })
            ->rawColumns(['action', 'visit', 'visitor', 'property', 'file'])
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
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        $visit_types=$this->condominium->visit_types()->orderBy('name')->pluck('name','id');
        $today=Carbon::now();

        if($id==0){
            $visit = new Visit();
        }else{
            $visit = Visit::find($id);
        }
        
        return view('visits.save')->with('visit', $visit)
                            ->with('today', $today)
                            ->with('properties', $properties)
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
            $visit->property_id=$request->property;
            $visit->user_id=Auth::user()->id;
            //se obtiene el visitante
            $visitor=$this->get_visitor($request);
            $visit->visitor_id=$visitor->id;
            //se obtiene el carro visitante
            $visiting_car=$this->get_visiting_car($request);
            $visit->visiting_car_id=$visiting_car->id;
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
            $visit->property_id=$request->property;
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
        $visit = Visit::find($id);
        
        return Storage::download($visit->condominium_id.'/visits/'.$visit->file, $visit->file_name);
    }
    
    function get_visitor($request){
        $condominium_id=$request->condominium_id;
        $nit=$request->NIT;
        if(Visitor::where('condominium_id', $condominium_id)->where('NIT', $nit)->exists()){
            $visitor=Visitor::where('condominium_id', $condominium_id)->where('NIT', $nit)->first();
        }else{
            $visitor=new Visitor();
            $visitor->condominium_id=$condominium_id;
            $visitor->NIT=$nit;
            $visitor->name=trim(strtoupper($request->name));
            $visitor->save();
        }
        return $visitor;
    }

    function get_visiting_car($request){
        $condominium_id=$request->condominium_id;
        $plate=trim(strtoupper($request->plate));
        if(VisitingCar::where('condominium_id', $condominium_id)->where('plate', $plate)->exists()){
            $visiting_car=VisitingCar::where('condominium_id', $condominium_id)->where('plate', $plate)->first();
        }else{
            $visiting_car=new VisitingCar();
            $visiting_car->condominium_id=$condominium_id;
            $visiting_car->plate=$plate;
            $visiting_car->make=trim(strtoupper($request->make));
            $visiting_car->model=trim(strtoupper($request->model));
            $visiting_car->save();
        }
        return $visiting_car;
    }

    public function get_visits_collection(Request $request){
        
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        $user_filter=$request->user_filter;
        $property_filter=$request->property_filter;

        if($user_filter!=''){
            if($property_filter!=''){
                $visits = $this->condominium->visits()
                            ->whereDate('checkin','>=', $start_filter)
                            ->whereDate('checkin','<=', $end_filter)
                            ->where('user_id', $user_filter)
                            ->where('property_id', $property_filter);
            }else{
                $visits = $this->condominium->visits()
                            ->whereDate('checkin','>=', $start_filter)
                            ->whereDate('checkin','<=', $end_filter)
                            ->where('user_id', $user_filter);
            }
        }else{
            if($property_filter!=''){
                $visits = $this->condominium->visits()
                            ->whereDate('checkin','>=', $start_filter)
                            ->whereDate('checkin','<=', $end_filter)
                            ->where('property_id', $property_filter);
            }else{
                $visits = $this->condominium->visits()
                            ->whereDate('checkin','>=', $start_filter)
                            ->whereDate('checkin','<=', $end_filter);
            }
        }
        return $visits;
    }

    public function rpt_visits(Request $request){
        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $visits=$this->get_visits_collection($request)->get();

        if($request->property_filter!=''){
            $property=Property::find($request->property_filter);
            $property_number=$property->number;
        }else{
            $property_number='Todas';
        }

        if($request->user_filter!=''){
            $user=User::find($request->user_filter);
            $user_name=$user->name;
        }else{
            $user_name='Todos';
        }

        $data=[
            'company' => $this->condominium->name,
            'logo' => $logo,            
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'user_name' => $user_name,
            'property_number' => $property_number,
            'visits' => $visits            
        ];

        $pdf = PDF::loadView('reports/rpt_visits', $data);
        
        return $pdf->stream('Visitas.pdf');        
    }
}
