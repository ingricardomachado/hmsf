<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
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
use Storage;

class AssetController extends Controller
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
     * Display a listing of the asset.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('assets.index');
    }

    public function datatable()
    {        

        $assets = $this->condominium->assets();        
        
        return Datatables::of($assets)
            ->addColumn('action', function ($asset) {
                $asset_id = Crypt::encrypt($asset->id);
                $url_edit = route('assets.edit', $asset_id);
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalAsset('.$asset->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$asset->id.'`, `'.$asset->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                })           
            ->editColumn('name', function ($asset) {                    
                    return '<a href="#"  onclick="showModalAsset('.$asset->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$asset->name.'</b><br><small>'.$asset->description.'</small></a>';
                })
            ->editColumn('cost', function ($asset) {                    
                    return session('coin').' '.money_fmt($asset->cost);
                })
            ->editColumn('total', function ($asset) {                    
                    return session('coin').' '.money_fmt($asset->total);
                })
            ->editColumn('status', function ($asset) {                    
                    return $asset->status_label;
                })
            ->rawColumns(['action', 'name', 'description', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified asset.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $asset = new Asset();
        }else{
            $asset = Asset::find($id);
        }
        
        return view('assets.save')->with('asset', $asset);
    }

    /**
     * Store a newly created asset in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssetRequest $request)
    {
        try {
            $asset = new Asset();
            $asset->condominium_id=$request->condominium_id;
            $asset->name=$request->name;
            $asset->description=$request->description;
            $asset->cost=$request->cost;
            $asset->quantity=$request->quantity;
            $asset->total=$asset->quantity*$asset->cost;
            $asset->status=$request->status;
            $asset->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Activo registrado exitosamente',
                    'asset' => $asset->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified asset in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssetRequest $request)
    {
        try {
            $asset = Asset::find($request->asset_id);
            $asset->name=$request->name;
            $asset->description=$request->description;
            $asset->cost=$request->cost;
            $asset->quantity=$request->quantity;
            $asset->total=$asset->quantity*$asset->cost;
            $asset->status=$request->status;
            $asset->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Activo actualizado exitosamente',
                    'asset' => $asset
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified asset from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $asset = Asset::find($id);
            $asset->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Activo eliminado exitosamente'
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
            $asset = Asset::find($id);
            ($asset->active)?$asset->active=false:$asset->active=true;
            $asset->save();

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
    
    public function rpt_assets()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'assets' => $this->condominium->assets()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_assets', $data);
        
        return $pdf->stream('Activos.pdf');

    }
}
