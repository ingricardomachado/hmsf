<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\OwnerRequest;
use App\User;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Mail;
use App\Mail\SignedupOwner;
use App\Mail\ChangePassword;

class OwnerController extends Controller
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
     * Display a listing of the owner.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('owners.index');
    }

    public function datatable()
    {        

        $owners = User::where('condominium_id', $this->condominium->id);        
        
        return Datatables::of($owners)
            ->addColumn('action', function ($owner) {
                $owner_id = Crypt::encrypt($owner->id);
                $url_edit = route('owners.edit', $owner_id);
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalOwner('.$owner->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$owner->id.'`, `'.$owner->name.'`, `'.$owner->credit_points.'`, `'.$owner->debit_points.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                })           
            ->editColumn('name', function ($owner) {                    
                    return '<a href="#"  onclick="showModalOwner('.$owner->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$owner->name.'</b><br><small><i>'.$owner->email.'</small></i></a>';
                })
            ->addColumn('properties', function ($owner) {                    
                    return $owner->properties_label;
                })
            ->editColumn('status', function ($owner) {                    
                    return $owner->status_label;
                })
            ->rawColumns(['action', 'name', 'properties', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified owner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $users=$this->condominium->users()->orderBy('name')->pluck('name','id');        
        $properties=$this->condominium->properties()->whereNull('user_id')->get();
        $owner_properties=[];
        
        if($id==0){
            $owner = new User();
        }else{
            $owner = User::find($id);
            $owner_properties=$owner->properties()->get();
        }
        
        return view('owners.save')->with('owner', $owner)
                            ->with('properties', $properties)
                            ->with('owner_properties', $owner_properties);
    }

    /**
     * Store a newly created owner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OwnerRequest $request)
    {
        try {
            $owner = new User();
            $owner->condominium_id=$this->condominium->id;
            $owner->name=$request->name;
            $owner->email=$request->email;
            $owner->cell=$request->cell;
            $owner->phone=$request->phone;
            $owner->role='OWN';
            $owner->password=bcrypt($request->password);
            $owner->committee=($request->committee)?true:false;
            $owner->save();
            Property::whereIn('id', $request->properties)->update(['user_id' => $owner->id]);
            if($request->notification){
                Mail::to($owner->email)->send(new SignedupOwner($owner, $request->password));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Propietario registrado exitosamente',
                    'owner' => $owner->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified owner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OwnerRequest $request, $id)
    {
        try {
            $owner = User::find($id);
            $owner->name=$request->name;
            $owner->email=$request->email;
            $owner->cell=$request->cell;
            $owner->phone=$request->phone;
            $owner->committee=($request->committee)?true:false;
            if($request->change_password){
                $owner->password=bcrypt($request->password);
                if($request->notification){
                    Mail::to($owner->email)->send(new ChangePassword($owner, $request->password));
                }
            }
            $owner->save();
            Property::where('user_id', $owner->id)->update(['user_id' => null]);
            Property::whereIn('id', $request->properties)->update(['user_id' => $owner->id]);

            return response()->json([
                    'success' => true,
                    'message' => 'Propietario actualizado exitosamente',
                    'owner' => $owner->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified owner from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $owner = User::find($id);
            if($owner->avatar){
                Storage::delete($owner->condominium_id.'/users/'.$user->avatar);
                Storage::delete($owner->condominium_id.'/users/thumbs/'.$user->avatar);
            }

            Property::where('user_id', $owner->id)->update(['user_id' => null]);
            $owner->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Propietario eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rpt_owners()
    {        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'owners' => $this->condominium->users()->orderBy('name')->get(),
            'logo' => $logo
        ];
        
        $pdf = PDF::loadView('reports/rpt_owners', $data);
        
        return $pdf->stream('Propietarios.pdf');

    }

}
