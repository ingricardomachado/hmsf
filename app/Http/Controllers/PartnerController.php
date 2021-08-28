<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PartnerRequest;
use App\User;
use App\Models\Partner;
use App\Models\State;
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
use App\Mail\SignedupPartner;
use App\Mail\ChangePassword;
use Storage;

class PartnerController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the partner.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('partners.index');
    }

    public function datatable()
    {        

        $partners = Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->select(['partners.*', 'users.full_name as full_name', 'users.email as email'])->orderBy('full_name');        
        
        return Datatables::of($partners)
            ->addColumn('action', function ($partner) {
                    if($partner->user->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="" class="modal-class" onclick="showModalPartner('.$partner->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$partner->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$partner->id.'`, `'.$partner->full_name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$partner->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('name', function ($partner) {                    
                    return '<a href="#"  onclick="showModalPartner('.$partner->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$partner->full_name.'</b><br><small><i>'.$partner->email.'</small></i></a>';
                })
            ->editColumn('tax', function ($partner) {                    
                    return $partner->tax.'%';
                })
            ->addColumn('customers', function ($partner) {                    
                    return "";
                })
            ->addColumn('operations', function ($partner) {                    
                    return "";
                })
            ->editColumn('status', function ($partner) {                    
                    return $partner->status_label;
                })
            ->rawColumns(['action', 'name', 'properties', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified partner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $states=State::orderBy('name')->pluck('name','id');        
        
        if($id==0){
            $partner = new Partner();
        }else{
            $partner = Partner::find($id);
        }
        
        return view('partners.save')->with('partner', $partner)
                            ->with('states', $states);
    }

    /**
     * Store a newly created partner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartnerRequest $request)
    {
        try {
            //registra el usuario
            $user = new User();
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->full_name=$user->first_name.' '.$user->last_name;
            $user->email=$request->email;
            $user->role='SOC';
            $user->password=bcrypt($request->password);
            $user->save();
            //registra el socio
            $partner = new Partner();
            $partner->number=Partner::max('number')+1;
            $partner->user_id=$user->id;
            $partner->state_id=$request->state;
            $partner->cell=$request->cell;
            $partner->phone=$request->phone;
            $partner->address=$request->address;
            $partner->tax=$request->tax;
            $partner->save();
            if($request->notification){
                //Mail::to($partner->email)->send(new SignedupPartner($partner, $request->password));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Socio Comercial registrado exitosamente',
                    'partner' => $partner->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified partner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PartnerRequest $request, $id)
    {
        try {
            $partner = Partner::findOrFail($id);
            //actualiza el usuario
            $user = User::findOrFail($partner->user_id);
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->full_name=$request->first_name.' '.$user->last_name;
            $user->email=$request->email;
            if($request->change_password){
                $user->password=bcrypt($request->password);
                if($request->notification){
                    //Mail::to($user->email)->send(new ChangePassword($user, $request->password));
                }
            }
            $user->save();
            //actualizar partner
            $partner->state_id=$request->state;
            $partner->cell=$request->cell;
            $partner->phone=$request->phone;
            $partner->address=$request->address;
            $partner->tax=$request->tax;
            $partner->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Socio Comercial actualizado exitosamente',
                    'partner' => $partner->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified partner from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $partner = Partner::findOrFail($id);
            if($partner->user->avatar){
                Storage::delete('users/'.$partner->user->avatar);
                Storage::delete('users/thumbs/'.$partner->user->avatar);
            }
            $partner->user->delete();
            $partner->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Socio Comercial eliminado exitosamente'
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
            $partner = Partner::findOrFail($id);
            ($partner->user->active)?$partner->user->active=false:$partner->user->active=true;
            $partner->user->save();

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
    
    public function rpt_partners()
    {        
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';

        $partners=Partner::join('users', 'partners.user_id', '=', 'users.id')
                        ->select(['partners.*', 'users.full_name as full_name', 'users.email as email'])->orderBy('full_name')->get();
        
        $data=[
            'company' => $setting->company,
            'partners' => $partners,
            'logo' => $logo
        ];
        
        $pdf = PDF::loadView('reports/rpt_partners', $data);
        
        return $pdf->stream('Socio Comercials.pdf');

    }

}
