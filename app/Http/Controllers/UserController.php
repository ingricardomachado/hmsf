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

    public function datatable(Request $request)
    {        
            $users = User::all();

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                $user_id = Crypt::encrypt($user->id);
                $url_edit = route('users.edit', $user_id);
                    if($user->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalUser('.$user->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$user->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$user_id.'`, `'.$user->full_name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$user->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('name', function ($user) {                    
                    return $user->name.'<br><small><i>'.$user->email.'</i></small>';
                })
            ->editColumn('role', function ($user) {                    
                    return $user->role_description;
                })
            ->editColumn('created_at', function ($user) {                    
                    return $user->created_at->format('d/m/Y H:i');
                })
            ->editColumn('status', function ($user) {                    
                    return $user->status_label;
                })
            ->rawColumns(['action','name','role','status'])
            ->make(true);
    }
    
    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load_user($user_id)
    {
        if($user_id==0){
            $user = new User();
        }else{
            $user = User::find($user_id);
        }
        
        return view('users.save')->with('user', $user);;
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User();        
        // Codigo para el avatar
        $file = Input::file('avatar');        
        if (File::exists($file))
        {        
            $user->avatar_name = $file->getClientOriginalName();
            $user->avatar_type = $file->getClientOriginalExtension();
            $user->avatar=$this->upload_file('/users/', $file);
        }        
        $user->name= $request->input('name');
        $user->email= $request->input('email');
        $user->role= $request->input('role');
        $user->password= password_hash($request->input('password'), PASSWORD_DEFAULT);
        $user->email_notification=($request->input('email_notification'))?1:0;
        $user->save();
        
        return redirect()->route('users.index')->with('notify', 'create');
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
        $user = User::find($id);        
        // Codigo para el logo
        $file = Input::file('avatar');
        if(File::exists($file))
        {        
            ($user->file)?Storage::delete('/users/'.$user->file):'';
            $user->avatar_name = $file->getClientOriginalName();
            $user->avatar_type = $file->getClientOriginalExtension();
            $user->avatar=$this->upload_file('/users/', $file);
        }
        $user->name= $request->input('name');
        $user->email= $request->input('email');
        $user->role= $request->input('role');
        if($request->input('change_password')){
            $user->password= password_hash($request->input('password'), PASSWORD_DEFAULT);
        }        
        $user->email_notification=($request->input('email_notification'))?1:0;
        $user->save();

        return redirect()->route('users.index')->with('notify', 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {        
        $user = User::find(Crypt::decrypt($id));
        if ($user->sales->count()==0 & $user->purchases->count()==0 & $user->open_closures->count()==0 & $user->close_closures->count()==0){            
            ($user->avatar)?Storage::delete('/users/'.$user->avatar):'';
            $user->delete();
            return redirect()->route('users.index')->with('notify', 'delete');        
        }else{            
            return redirect()->route('users.index')->withErrors('No se puede eliminar el usuario porque tiene información asociada en el sistema. Si quiere, puede deshabilitarlo.');            
        }
    }

    /**
     * Update the status to specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        $user = User::find($request->user_id);
        ($user->active)?$user->active=false:$user->status=true;
        $user->save();
    }

    public function rpt_users()
    {        
        $setting = Setting::first();
        $users=User::orderBy('name')->get();
        $logo='data:image/png;base64, '.$setting->logo;
        $company=$setting->company;
        
        $data=[
            'company' => $company,
            'users' => $users,
            'logo' => $logo
        ];
                
        $pdf = PDF::loadView('reports/rpt_users', $data);
        
        return $pdf->stream('Usuarios.pdf');

    }

}
