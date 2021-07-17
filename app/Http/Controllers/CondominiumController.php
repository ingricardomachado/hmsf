<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CondominiumRequest;
use App\User;
use App\Models\Condominium;
use App\Models\Country;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use Carbon\Carbon;
use Image;
use File;
use DB;
use PDF;
use Auth;

class CondominiumController extends Controller
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
     * Display a listing of the condominium.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('condominiums.index');
    }

    public function datatable()
    {        

        $condominiums = Condominium::all();        
        
        return Datatables::of($condominiums)
            ->addColumn('action', function ($condominium) {
                $condominium_id = Crypt::encrypt($condominium->id);
                $url_edit = route('condominiums.edit', $condominium_id);
                    if($condominium->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalCondominium('.$condominium->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$condominium->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$condominium->id.'`, `'.$condominium->plate.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$condominium->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('name', function ($condominium) {                    
                    return '<a href="#"  onclick="showModalCondominium('.$condominium->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$condominium->name.'</b><br><small><i>'.$condominium->country->name.'</small></i></a>';
                })
            ->editColumn('contact', function ($condominium) {                    
                    return $condominium->contact.'<br><small>'.$condominium->cell.'</small>';
                })
            ->editColumn('type', function ($condominium) {                    
                    return $condominium->type_description;
                })
            ->editColumn('status', function ($condominium) {                    
                    return $condominium->status_label;
                })
            ->rawColumns(['action', 'name', 'contact', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified condominium.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $countries=Country::orderBy('name')->pluck('name','id');
        $property_types=PropertyType::orderBy('name')->pluck('name','id');


        if($id==0){
            $condominium = new Condominium();
            $states=[];
        }else{
            $condominium = Condominium::find($id);
            $country=Country::find($condominium->country_id);
            $states=$country->states()->orderBy('name')->pluck('name','id');
        }
        
        return view('condominiums.save')->with('condominium', $condominium)
                                ->with('property_types', $property_types)
                                ->with('countries', $countries)
                                ->with('states', $states);
    }

    /**
     * Store a newly created condominium in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CondominiumRequest $request)
    {
        try {
            $condominium = new Condominium();
            $condominium->type=$request->type;
            $condominium->property_type_id=$request->property_type;
            $condominium->name=$request->name;
            $condominium->country_id=$request->country;
            $condominium->state_id=$request->state;
            $condominium->max_properties=$request->max_properties;
            $condominium->contact=$request->contact;
            $condominium->cell=$request->cell;
            $condominium->phone=$request->phone;
            $condominium->email=$request->email;
            $condominium->save();
            //Registra el usuario administrador
            $user=new User();
            $user->condominium_id=$condominium->id;
            $user->name=$request->contact;
            $user->email=$request->email;
            $user->cell=$request->cell;
            $user->role='ADM';
            $user->active=1;
            $user->password=bcrypt($request->password);
            $user->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Condominio registrado exitosamente',
                    'condominium' => $condominium->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified condominium in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CondominiumRequest $request, $id)
    {
        try {
            $condominium = Condominium::find($id);
            $condominium->type=$request->type;
            $condominium->property_type_id=$request->property_type;
            $condominium->name=$request->name;
            $condominium->country_id=$request->country;
            $condominium->state_id=$request->state;
            $condominium->max_properties=$request->max_properties;
            $condominium->contact=$request->contact;
            $condominium->cell=$request->cell;
            $condominium->phone=$request->phone;
            $condominium->email=$request->email;
            $condominium->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Condominio actualizado exitosamente',
                    'condominium' => $condominium
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified condominium from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $condominium = Condominium::find($id);
            $condominium->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Condominio eliminado exitosamente'
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
            $condominium = Condominium::find($id);
            ($condominium->active)?$condominium->active=false:$condominium->active=true;
            $condominium->save();

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
    
    public function rpt_condominiums()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'condominiums' => $this->condominium->condominiums()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_condominiums', $data);
        
        return $pdf->stream('Condominios.pdf');

    }

    public function events(Request $request, $id)
    {        
        $start=Carbon::createFromFormat('Y-m-d', $request->start)->format('Y-m-d');
        $end=Carbon::createFromFormat('Y-m-d', $request->end)->format('Y-m-d');
        $condominium=Condominium::find($id);
        $events=$condominium->events()
                            ->whereDate('start','>=',$start)
                            ->whereDate('start','<=',$end)->get();
        
        $data = array();
        $i=0;
        foreach ($events as $event) {
            $data[$i++] = array(
                "id"=>$event->id,
                "title"=>$event->title,
                "start"=>$event->start->format('Y-m-d H:i'),
                "end"=>($event->end)?$event->end->format('Y-m-d H:i'):'',
                "allDay"=>$event->all_day,
                "color"=>$event->color,
                "private"=>$event->private,
                "editable"=>true
            );
        }
 
        return response()->json($data);
        
    }

}
