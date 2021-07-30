<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\UserRequest;
use Auth;
use Illuminate\Support\Facades\Crypt;
use Session;
use App\User;
use App\Models\Setting;
use App\Models\Center;
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

class UserController extends Controller
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
        return view('users.index');
    }

    public function datatable()
    {        

        $users = $this->condominium->users()->whereIn('role', ['ADM', 'WAM']);        
        
        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalUser('.$user->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$user->id.'`, `'.$user->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                })           
            ->editColumn('name', function ($user) {                    
                    return '<a href="#"  onclick="showModalUser('.$user->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$user->name.'</b><br><small><i>'.$user->email.'</small></i></a>';
                })
            ->editColumn('role', function ($user) {                    
                    return $user->role_description;
                })
            ->editColumn('status', function ($user) {                    
                    return $user->status_label;
                })
            ->rawColumns(['action', 'name', 'role', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $user = new User();
        }else{
            $user = User::find($id);
        }
        
        return view('users.save')->with('user', $user);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            $user = new User();
            $user->condominium_id=$this->condominium->id;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->cell=$request->cell;
            $user->phone=$request->phone;
            $user->role=$request->role;
            $user->password=bcrypt($request->password);
            $user->save();
            if($request->notification){
                Mail::to($user->email)->send(new SignedupOwner($user, $request->password));
            }
            
            return response()->json([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente',
                    'user' => $user->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::find($id);
            $user->name=$request->name;
            $user->email=$request->email;
            $user->cell=$request->cell;
            $user->phone=$request->phone;
            $user->role=$request->role;
            if($request->change_password){
                $user->password=bcrypt($request->password);
                if($request->notification){
                    Mail::to($user->email)->send(new ChangePassword($user, $request->password));
                }
            }
            $user->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente',
                    'user' => $user
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
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
            $user = User::find($id);
            ($user->active)?$user->active=false:$user->active=true;
            $user->save();

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
    
    public function rpt_users()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'users' => $this->condominium->users()->whereIn('role', ['ADM', 'WAM'])->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_users', $data);
        
        return $pdf->stream('Usuarios.pdf');

    }
}
