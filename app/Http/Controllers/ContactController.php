<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ContactRequest;
use Auth;
use Illuminate\Support\Facades\Crypt;
use Session;
use App\Models\Contact;
use App\User;
use App\Models\Setting;
use App\Models\Center;
use App\Models\State;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Storage;
use Image;
use File;
use DB;
use PDF;
use Mail;

class ContactController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('contacts.index');
    }

    public function datatable(Request $request)
    {        

        $contacts=$this->condominium->contacts();

        return Datatables::of($contacts)
            ->addColumn('action', function ($contact) {
                return '<div class="input-group-btn text-center">
                    <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" name="" class="modal-class" onclick="showModalContact('.$contact->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                        </li>                        
                        <li class="divider"></li>
                        <li>
                            <a href="#" onclick="showModalDelete(`'.$contact->id.'`, `'.$contact->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                        </li>
                    </ul>
                </div>';
                })           
            ->editColumn('name', function ($contact) {                    
                    if($contact->position){
                        return '<a href="#"  onclick="showModalShowContact('.$contact->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$contact->name.'</b><br><small><i>'.$contact->position.' <br><b>'.$contact->company.'</b></small></i></a>';
                    }else{
                        return '<a href="#"  onclick="showModalShowContact('.$contact->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$contact->name.'</a>';
                    }
                })
            ->editColumn('address', function ($contact) {                    
                    return '<small>'.$contact->address.'</small>';
                })
            ->editColumn('status', function ($contact) {                    
                    return $contact->status_label;
                })
            ->rawColumns(['action','name','address','status'])            
            ->make(true);
    }
        
    /**
     * Display the specified contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $contact = new Contact();
        }else{
            $contact = Contact::find($id);
        }
        
        return view('contacts.save')->with('contact', $contact);
    }
    
    /**
     * Display the specified contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Contact::find($id);
        
        return view('contacts.show')->with('contact', $contact);
    }

    /**
     * Store a newly created contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        try {
            $condominium_id=$request->condominium_id;
            $contact = new Contact();        
            $file = $request->avatar;        
            if (File::exists($file))
            {        
                $contact->avatar_name = $file->getClientOriginalName();
                $contact->avatar_type = $file->getClientOriginalExtension();
                $contact->avatar_size = $file->getSize();
                $contact->avatar=$this->upload_file($condominium_id.'/contacts/', $file);
            }        
            $contact->condominium_id=$condominium_id;
            $contact->name= $request->name;
            $contact->company=$request->company;
            $contact->position= $request->position;
            $contact->email= $request->email;
            $contact->phone= $request->phone;
            $contact->cell= $request->cell;
            $contact->address= $request->address;
            $contact->twitter= $request->twitter;
            $contact->facebook= $request->facebook;
            $contact->instagram= $request->instagram;
            $contact->about= $request->about;
            $contact->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Contacto registrado exitosamente',
                    'contact' => $contact->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Update the specified contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request, $id)
    {
        try {
            $contact = Contact::find($id);        
            $file = $request->avatar;
            if(File::exists($file))
            {        
                if($contact->avatar){
                    Storage::delete($contact->condominium_id.'/contacts/'.$contact->avatar);   
                    Storage::delete($contact->condominium_id.'/contacts/thumbs/'.$contact->avatar);
                }
                $contact->avatar_name = $file->getClientOriginalName();
                $contact->avatar_type = $file->getClientOriginalExtension();
                $contact->avatar_size = $file->getSize();
                $contact->avatar=$this->upload_file($contact->condominium_id.'/contacts/', $file);
            }
            $contact->name= $request->name;
            $contact->company=$request->company;
            $contact->position= $request->position;
            $contact->email= $request->email;
            $contact->phone= $request->phone;
            $contact->cell= $request->cell;
            $contact->address= $request->address;
            $contact->twitter= $request->twitter;
            $contact->facebook= $request->facebook;
            $contact->instagram= $request->instagram;
            $contact->about= $request->about;
            $contact->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Contacto actualizado exitosamente',
                    'contact' => $contact
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        try {
            $contact = Contact::find($id);
            if($contact->avatar){
                Storage::delete($contact->condominium_id.'/contacts/'.$contact->avatar);
                Storage::delete($contact->condominium_id.'/contacts/thumbs/'.$contact->avatar);
            }
            $contact->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Contacto eliminado exitosamente'
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
            $contact = Contact::find($id);
            ($contact->active)?$contact->active=false:$contact->active=true;
            $contact->save();

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
    
    public function rpt_contacts()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'contacts' => $this->condominium->contacts()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_contacts', $data);
        
        return $pdf->stream('Contactos.pdf');

    }

}
