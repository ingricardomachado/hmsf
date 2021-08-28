<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CenterRequest;
use App\User;
use App\Models\Center;
use App\Models\State;
use App\Models\Setting;
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
use Storage;

class CenterController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the center.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('centers.index');
    }

    /**
     * Display a listing of the center.
     *
     * @return \Illuminate\Http\Response
     */
    public function demos()
    {                
        return view('centers.index');
    }

    public function datatable(Request $request)
    {        
        $centers = Center::orderBy('name');        
        
        return Datatables::of($centers)
            ->addColumn('action', function ($center){
                    if($center->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalCenter('.$center->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$center->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$center->id.'`, `'.$center->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$center->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('name', function ($center) {                    
                    return '<a href="#"  onclick="showModalCenter('.$center->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$center->name.'</b><br><small><i>'.$center->state->name.'</small></i></a>';
                })
            ->editColumn('status', function ($center) {                    
                    return $center->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified center.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $states=State::orderBy('name')->pluck('name','id');

        if($id==0){
            $center = new Center();
        }else{
            $center = Center::find($id);
        }
        
        return view('centers.save')->with('center', $center)
                                ->with('states', $states);
    }

    /**
     * Store a newly created center in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CenterRequest $request)
    {
        try {
            $center = new Center();
            $center->name=$request->name;
            $center->state_id=$request->state;
            $center->city=$request->city;
            $center->address=$request->address;
            $center->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Oficina registrada exitosamente',
                    'center' => $center->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified center in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CenterRequest $request, $id)
    {
        try {
            $center = Center::find($id);
            $center->name=$request->name;
            $center->state_id=$request->state;
            $center->city=$request->city;
            $center->address=$request->address;
            $center->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Oficina actualizada exitosamente',
                    'center' => $center
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified center from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $center = Center::find($id);
            Storage::deleteDirectory($center->id);
            $center->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Oficina eliminada exitosamente'
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
            $center = Center::find($id);
            ($center->active)?$center->active=false:$center->active=true;
            $center->save();

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
    
    public function rpt_centers()
    {        
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';

        $centers=Center::orderBy('name')->get();
                
        $data=[
            'company' => $setting->company,
            'centers' => $centers,
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_centers', $data);
        
        return $pdf->stream('Oficinas.pdf');
    }
}
