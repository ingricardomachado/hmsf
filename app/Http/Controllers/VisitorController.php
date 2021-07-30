<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VisitorRequest;
use App\Models\Account;
use App\Models\Visitor;
use App\Models\VisitorType;
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

class VisitorController extends Controller
{
           
    /**
     * Display a listing of the visitor.
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
            $visitor = Visitor::findOrFail($id);
            
            return response()->json([
                $visitor->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }        
    }


    /**
     * Store a newly created visitor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $visitor=new Visitor();
            $visitor->condominium_id=$request->condominium_id;
            $visitor->NIT=$request->condominium_id;
            $visitor->name=$request->name;
            $visitor->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Visitante registrado exitosamente',
                    'visitor' => $visitor->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified visitor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VisitorRequest $request, $id)
    {
        try {
            $visitor = Visitor::find($id);
            $visitor->NIT=$request->condominium_id;
            $visitor->name=$request->name;
            $visitor->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Visitante actualizado exitosamente',
                    'visitor' => $visitor->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified visitor from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $visitor = Visitor::find($id);
            $visitor->delete();
                        
            return response()->json([
                'success' => true,
                'message' => 'Visitante eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }    

    public function visitor_by_nit($nit)
    {                
        try {
            if(Visitor::where('NIT', $nit)->exists()){

                $visitor=Visitor::where('NIT', $nit)->first();
                
            return response()->json([
                    'success' => true,
                    'visitor' => $visitor->toArray()
                ], 200);

            }else{

            }
            
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }        
    }

}
