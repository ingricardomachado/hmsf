<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use App\Exports\PropertiesExport;
use Storage;
use Image;
use File;
use DB;
use PDF;
use Auth;

class DocumentController extends Controller
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
     * Display a listing of the document.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $document_types=$this->condominium->document_types()->orderBy('name')->pluck('name','id');
        
        return view('documents.index')->with('document_types', $document_types);
    }

    public function datatable(Request $request)
    {        
        $document_type_filter=$request->document_type_filter;

        if($document_type_filter!=''){
            $documents = $this->condominium->documents()->where('document_type_id', $document_type_filter);
        }else{
            $documents = $this->condominium->documents();
        }
                
        return Datatables::of($documents)
            ->addColumn('action', function ($document) {
                $icon_public=($document->public)?'fa fa-lock':'fa fa-unlock';
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalDocument('.$document->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                            </li>
                            <li>
                                <a href="#" name="href_status" class="modal-class" onclick="change_visibility('.$document->id.')"><i class="'.$icon_public.'"></i> Cambiar visibilidad</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$document->id.'`, `'.$document->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('name', function ($document) {                    
                    return '<a href="#"  onclick="showModalDocument('.$document->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$document->name.'</b><br><small><i>'.$document->document_type->name.'</small></i></a>';
                })
            ->editColumn('description', function ($document) {                    
                    return '<small>'.$document->description.'</small>';
                })
            ->editColumn('created_at', function ($document) {
                    if($document->public){
                        return '<small class=text-muted><i class="fa fa-unlock" aria-hidden="true" title="privado"></i> '.$document->created_at->format('d.m.Y H:i').'<br> Tamaño '.round($document->file_size/1000,1).' Kb <small>';
                    }else{
                        return '<small class=text-muted><i class="fa fa-lock" aria-hidden="true" title="publico"></i> '.$document->created_at->format('d.m.Y H:i').'<br> Tamaño '.round($document->file_size/1000,1).' Kb <small>';
                    }
                })
            ->addColumn('file', function ($document) {
                    if($document->file_name){                    
                        $ext=$document->file_type;
                        if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                            $url_show_file = url('document_photo', $document->id);
                            return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$document->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
                        }else{
                            $url_download_file = route('documents.download', $document->id);
                            return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$document->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
                        }
                    }
                })
            ->rawColumns(['action', 'name', 'description', 'created_at', 'file'])
            ->make(true);
    }
    
    /**
     * Display the specified document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $document_types=$this->condominium->document_types()->orderBy('name')->pluck('name','id');

        if($id==0){
            $document = new Document();
        }else{
            $document = Document::find($id);
        }
        
        return view('documents.save')->with('document', $document)
                            ->with('document_types', $document_types);
    }

    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
        try {
            $document = new Document();
            $document->condominium_id=$request->condominium_id;
            $document->created_by=Auth::user()->name;
            $document->document_type_id=$request->document_type;
            $document->name=$request->name;
            $document->description=$request->description;
            $file = $request->file;
            if (File::exists($file)){
                $document->file_name = $file->getClientOriginalName();
                $document->file_type = $file->getClientOriginalExtension();
                $document->file_size = $file->getSize();
                $document->file=$this->upload_file($document->condominium_id.'/documents/', $file);
            }        
            $document->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Documento registrado exitosamente',
                    'document' => $document->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentRequest $request, $id)
    {
        try {
            $document = Document::find($id);
            $document->document_type_id=$request->document_type;
            $document->name=$request->name;
            $document->description=$request->description;
            $document->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Documento actualizado exitosamente',
                    'document' => $document
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified document from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $document = Document::find($id);
            Storage::delete($document->condominium_id.'/documents/'.$document->file);
            Storage::delete($document->condominium_id.'/documents/thumbs/'.$document->file);
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function visibility($id)
    {
        try {
            $document = Document::find($id);
            ($document->public)?$document->public=false:$document->public=true;
            $document->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Visibilidad cambiada exitosamente',
                ], 200);                        

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);            
        }
    }
    
    public function rpt_documents()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'documents' => $this->condominium->documents()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_documents', $data);
        
        return $pdf->stream('Documentos.pdf');

    }

    /*
     * Download file from DB  
    */ 
    public function download_file($id)
    {
        $document = Document::find($id);
        
        return Storage::download($document->condominium_id.'/documents/'.$document->file, $document->file_name);
    }
}
