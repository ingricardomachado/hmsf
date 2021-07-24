<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProfileRequest;
use App\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Crypt;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Session;
use Image;
use File;
use DB;
use Auth;
use Storage;


class ProfileController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['edit']]);
    }    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user=User::find(Auth::user()->id);
        return view('profiles.save')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        try {
            
            $user = User::find(Auth::user()->id);
            $user->name=$request->name;
            $user->email=$request->email;
            $user->cell=$request->cell;
            $user->phone=$request->phone;
            ($request->change_password)?$user->password=bcrypt($request->password):'';
            $file = $request->avatar;
            if (File::exists($file)){
                if($user->condominium_id){
                    Storage::delete($user->condominium_id.'/users/'.$user->avatar);
                    Storage::delete($user->condominium_id.'/users/thumbs/'.$user->avatar);
                    $user->avatar_name = $file->getClientOriginalName();
                    $user->avatar_type = $file->getClientOriginalExtension();
                    $user->avatar_size = $file->getSize();
                    $user->avatar=$this->upload_file($user->condominium_id.'/users', $file);
                }else{
                    Storage::delete('global/users'.$user->avatar);
                    Storage::delete('/global/users/thumbs/'.$user->avatar);
                    $user->avatar_name = $file->getClientOriginalName();
                    $user->avatar_type = $file->getClientOriginalExtension();
                    $user->avatar_size = $file->getSize();
                    $user->avatar=$this->upload_file('global', $file);                
                }
            }
            $user->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Perfil actualizado exitosamente'
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
        //
    }

}
