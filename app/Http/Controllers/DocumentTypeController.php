<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DocumentTypeRequest;
use App\Models\DocumentType;
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

class DocumentTypeController extends Controller
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
     * Display a listing of the document_type.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('document_types.index');
    }

    public function datatable()
    {        

        $document_types = $this->condominium->document_types();        
        
        return Datatables::of($document_types)
            ->addColumn('action', function ($document_type) {
                    if($document_type->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalDocumentType('.$document_type->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$document_type->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$document_type->id.'`, `'.$document_type->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$document_type->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->editColumn('name', function ($document_type) {                    
                    return '<a href="#"  onclick="showModalDocumentType('.$document_type->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$document_type->name.'</a>';
                })
            ->editColumn('status', function ($document_type) {                    
                    return $document_type->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified document_type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $document_type = new DocumentType();
        }else{
            $document_type = DocumentType::find($id);
        }
        
        return view('document_types.save')->with('document_type', $document_type);
    }

    /**
     * Store a newly created document_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentTypeRequest $request)
    {
        try {
            $document_type = new DocumentType();
            $document_type->condominium_id=$this->condominium->id;
            $document_type->name=$request->name;
            $document_type->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Clasificación registrada exitosamente',
                    'document_type' => $document_type->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified document_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentTypeRequest $request, $id)
    {
        try {
            $document_type = DocumentType::find($id);
            $document_type->name=$request->name;
            $document_type->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Clasificación actualizada exitosamente',
                    'document_type' => $document_type
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified document_type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $document_type = DocumentType::find($id);
            $document_type->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Clasificación eliminada exitosamente'
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
            $document_type = DocumentType::find($id);
            ($document_type->active)?$document_type->active=false:$document_type->active=true;
            $document_type->save();

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
    
    public function rpt_document_types()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'document_types' => $this->condominium->document_types()->orderBy('name')->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_document_types', $data);
        
        return $pdf->stream('Clasificacións.pdf');

    }
}
