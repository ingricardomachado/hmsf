<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\FacilityRequest;
use App\Models\Facility;
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
use Storage;
use Image;
use File;
use DB;
use PDF;
use Auth;

class FacilityController extends Controller
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
     * Display a listing of the facility.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        return view('facilities.index');
    }

    public function datatable()
    {        

        $facilities = $this->condominium->facilities();        
        
        return Datatables::of($facilities)
            ->addColumn('action', function ($facility) {
                    if(session('role')=='ADM'){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalFacility('.$facility->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$facility->id.'`, `'.$facility->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }    
                })           
            ->editColumn('name', function ($facility) {                    
                    if(session('role')=='OWN'){
                        return '<b>'.$facility->name.'</b><br><small><i>De '.$facility->start->format('g:i a').' a '.$facility->end->format('g:i a');
                    }else{
                        return '<a href="#"  onclick="showModalFacility('.$facility->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$facility->name.'</b><br><small><i>De '.$facility->start->format('g:i a').' a '.$facility->end->format('g:i a').'</i></small></a>';
                    }
                })
            ->editColumn('more', function ($facility) {                    
                    $defaulters=($facility->defaulters)?'Si':'No';
                    if($facility->rent){
                        return '<small>Morosos pueden reservar: '.$defaulters.'<br>Costo día: '.session('coin').money_fmt($facility->day_cost).'<br> Costo hora: '.session('coin').''.money_fmt($facility->hour_cost);
                    }else{
                        return '<small>Morosos pueden reservar: '.$defaulters;
                    }
                })
            ->editColumn('status', function ($facility) {                    
                    return $facility->status_label;
                })
            ->rawColumns(['action', 'name', 'more', 'hours', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified facility.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $facility = new Facility();
        }else{
            $facility = Facility::find($id);
        }
        
        return view('facilities.save')->with('facility', $facility);
    }

    /**
     * Display the specified facility.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $facility = Facility::find($id);
        
        return view('facilities.show')->with('facility', $facility);
    }
    
    /**
     * Store a newly created facility in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilityRequest $request)
    {
        try {
            $condominium_id=$request->condominium_id;
            $facility = new Facility();
            $file = $request->photo;        
            if (File::exists($file))
            {        
                $facility->photo_name = $file->getClientOriginalName();
                $facility->photo_type = $file->getClientOriginalExtension();
                $facility->photo_size = $file->getSize();
                $facility->photo=$this->upload_file($condominium_id.'/facilities/', $file);
            }
            $facility->condominium_id=$condominium_id;
            $facility->name=$request->name;
            $facility->rules=$request->rules;
            $facility->start=Carbon::createFromFormat('H:i', $request->start);
            $facility->end=Carbon::createFromFormat('H:i', $request->end);
            $facility->defaulters=($request->defaulters)?true:false;
            $facility->rent=($request->rent)?true:false;
            if($facility->rent){
                $facility->day_cost=$request->day_cost;
                $facility->hour_cost=$request->hour_cost;
            }
            $facility->status=$request->status;
            $facility->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Instalación registrada exitosamente',
                    'facility' => $facility->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified facility in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FacilityRequest $request, $id)
    {
        try {
            $facility = Facility::find($id);
            $file = $request->photo;
            if(File::exists($file))
            {        
                if($facility->photo){
                    Storage::delete($facility->condominium_id.'/facilities/'.$facility->photo);   
                    Storage::delete($facility->condominium_id.'/facilities/thumbs/'.$facility->photo);
                }
                $facility->photo_name = $file->getClientOriginalName();
                $facility->photo_type = $file->getClientOriginalExtension();
                $facility->photo_size = $file->getSize();
                $facility->photo=$this->upload_file($facility->condominium_id.'/facilities/', $file);
            }
            $facility->name=$request->name;
            $facility->rules=$request->rules;
            $facility->start=Carbon::createFromFormat('H:i', $request->start);
            $facility->end=Carbon::createFromFormat('H:i', $request->end);
            $facility->defaulters=($request->defaulters)?true:false;
            $facility->rent=($request->rent)?true:false;
            if($facility->rent){
                $facility->day_cost=$request->day_cost;
                $facility->hour_cost=$request->hour_cost;
            }
            $facility->status=$request->status;
            $facility->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Instalación actualizada exitosamente',
                    'facility' => $facility
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified facility from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $facility = Facility::find($id);
            if($facility->photo){
                Storage::delete($facility->condominium_id.'/facilities/'.$facility->photo);
                Storage::delete($facility->condominium_id.'/facilities/thumbs/'.$facility->photo);
            }
            $facility->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Instalación eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function rpt_facilities()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'facilities' => $this->condominium->facilities()->orderBy('name')->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_facilities', $data);
        
        return $pdf->stream('Instalacións.pdf');

    }


    public function reservations(Request $request, $id)
    {        
        $start=Carbon::createFromFormat('Y-m-d', $request->start)->format('Y-m-d');
        $end=Carbon::createFromFormat('Y-m-d', $request->end)->format('Y-m-d');
        $facility=Facility::find($id);
        $reservations=$facility->reservations()
                            ->whereDate('start','>=',$start)
                            ->whereDate('start','<=',$end)->get();
        
        $data = array();
        $i=0;
        foreach ($reservations as $reservation) {
            $data[$i++] = array(
                "id"=>$reservation->id,
                "title"=>$reservation->property->number,
                "property"=>$reservation->property->number,
                "start"=>$reservation->start->format('Y-m-d H:i'),
                "end"=>($reservation->end)?$reservation->end->format('Y-m-d H:i'):'',
                "allDay"=>$reservation->all_day,
                "backgroundColor"=>$reservation->bgcolor,
                "borderColor"=>$reservation->bgcolor,
                "editable"=>false
            );
        }
 
        return response()->json($data);
        
    }

}
