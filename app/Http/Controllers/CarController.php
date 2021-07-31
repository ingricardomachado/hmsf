<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CarRequest;
use App\Models\Car;
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

class CarController extends Controller
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
     * Display a listing of the car.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('cars.index');
    }

    public function datatable()
    {        

        $cars = $this->condominium->cars();        
        
        return Datatables::of($cars)
            ->addColumn('action', function ($car) {
                $car_id = Crypt::encrypt($car->id);
                $url_edit = route('cars.edit', $car_id);
                    if($car->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalCar('.$car->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$car->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$car->id.'`, `'.$car->plate.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$car->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('plate', function ($car) {                    
                    return '<a href="#"  onclick="showModalCar('.$car->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$car->plate.'</b><br><small><i>'.$car->property->number.'</small></i></a>';
                })
            ->editColumn('notes', function ($car) {                    
                    return '<small>'.$car->notes.'</small>';
                })
            ->editColumn('status', function ($car) {                    
                    return $car->status_label;
                })
            ->rawColumns(['action', 'plate', 'notes', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified car.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');

        if($id==0){
            $car = new Car();
        }else{
            $car = Car::find($id);
        }
        
        return view('cars.save')->with('car', $car)
                                ->with('properties', $properties);
    }

    /**
     * Store a newly created car in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CarRequest $request)
    {
        try {
            $car = new Car();
            $property=Property::find($request->property);
            $car->property_id=$property->id;
            $car->condominium_id=$property->condominium_id;
            $car->plate=strtoupper($request->plate);
            $car->make=$request->make;
            $car->model=$request->model;
            $car->color=$request->color;
            $car->year=$request->year;
            $car->notes=$request->notes;
            $car->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Vehículo registrado exitosamente',
                    'car' => $car->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified car in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CarRequest $request, $id)
    {
        try {
            $car = Car::find($id);
            $property=Property::find($request->property);
            $car->property_id=$property->id;
            $car->plate=strtoupper($request->plate);
            $car->make=$request->make;
            $car->model=$request->model;
            $car->color=$request->color;
            $car->year=$request->year;
            $car->notes=$request->notes;
            $car->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Vehículo actualizado exitosamente',
                    'car' => $car
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified car from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $car = Car::find($id);
            $car->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente'
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
            $car = Car::find($id);
            ($car->active)?$car->active=false:$car->active=true;
            $car->save();

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
    
    public function rpt_cars()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'cars' => $this->condominium->cars()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_cars', $data);
        
        return $pdf->stream('Vehículos.pdf');

    }
}
