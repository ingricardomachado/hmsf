<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VisitingCarRequest;
use App\Models\VisitingCar;
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

class VisitingCarController extends Controller
{
           
    /**
     * Display a listing of the visiting_car.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        //
    }
    
    public function show($id)
    {                
        try {
            $visiting_car = VisitingCar::findOrFail($id);
            
            return response()->json([
                $visiting_car->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }        
    }


    /**
     * Store a newly created visiting_car in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $visiting_car=new VisitingCar();
            $visiting_car->condominium_id=$request->condominium_id;
            $visiting_car->plate=$request->plate;
            $visiting_car->make=$request->make;
            $visiting_car->model=$request->model;
            $visiting_car->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Vehiculo visitante registrado exitosamente',
                    'visiting_car' => $visiting_car->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified visiting_car in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VisitingCarRequest $request, $id)
    {
        try {
            $visiting_car = VisitingCar::find($id);
            $visiting_car->plate=$request->plate;
            $visiting_car->make=$request->make;
            $visiting_car->model=$request->model;
            $visiting_car->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Vehículo visitante actualizado exitosamente',
                    'visiting_car' => $visiting_car->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified visiting_car from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $visiting_car = VisitingCar::find($id);
            $visiting_car->delete();
                        
            return response()->json([
                'success' => true,
                'message' => 'Vehiculo visitante eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }    

    public function visiting_car_by_plate($condominium_id, $plate)
    {                
        try {
            if(VisitingCar::where('condominium_id', $condominium_id)->where('plate', $plate)->exists()){
                $visiting_car=VisitingCar::where('condominium_id', $condominium_id)->where('plate', $plate)->first();
                
                return response()->json([
                        'success' => true,
                        'visiting_car' => $visiting_car->toArray()
                    ], 200);
            }else{
                return response()->json([
                        'success' => false,
                        'message' => 'Registro no encontrado',
                    ], 404);

            }

        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }        
    }
}
