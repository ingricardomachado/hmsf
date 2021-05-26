<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\EmployeeRequest;
use Auth;
use Illuminate\Support\Facades\Crypt;
use Session;
use App\Models\Employee;
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

class EmployeeController extends Controller
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
        return view('employees.index');
    }

    public function datatable(Request $request)
    {        

        $employees=$this->condominium->employees();

        return Datatables::of($employees)
            ->addColumn('action', function ($employee) {
                    if($employee->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="" class="modal-class" onclick="showModalEmployee('.$employee->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$employee->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$employee->id.'`, `'.$employee->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$employee->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('name', function ($employee) {                    
                    return '<a href="#"  onclick="showModalShowEmployee('.$employee->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$employee->name.'</b><br><small><i>'.$employee->position.'</small></i></a>';
                })
            ->editColumn('address', function ($employee) {                    
                    return '<small>'.$employee->address.'</small>';
                })
            ->editColumn('status', function ($employee) {                    
                    return $employee->status_label;
                })
            ->rawColumns(['action','name','address','status'])            
            ->make(true);
    }
        
    /**
     * Display the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $employee = new Employee();
        }else{
            $employee = Employee::find($id);
        }
        
        return view('employees.save')->with('employee', $employee);
    }
    
    /**
     * Display the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        
        return view('employees.show')->with('employee', $employee);
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $condominium_id=$request->condominium_id;
            $employee = new Employee();        
            $file = $request->avatar;        
            if (File::exists($file))
            {        
                $employee->avatar_name = $file->getClientOriginalName();
                $employee->avatar_type = $file->getClientOriginalExtension();
                $employee->avatar_size = $file->getSize();
                $employee->avatar=$this->upload_file($condominium_id.'/employees/', $file);
            }        
            $employee->condominium_id=$condominium_id;
            $employee->name= $request->name;
            $employee->position= $request->position;
            $employee->NIT= $request->NIT;
            $employee->email= $request->email;
            $employee->phone= $request->phone;
            $employee->cell= $request->cell;
            $employee->address= $request->address;
            $employee->notes= $request->notes;
            $employee->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Empleado registrado exitosamente',
                    'employee' => $employee->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::find($id);        
            $file = $request->avatar;
            if(File::exists($file))
            {        
                if($employee->avatar){
                    Storage::delete($employee->condominium_id.'/employees/'.$employee->avatar);   
                    Storage::delete($employee->condominium_id.'/employees/thumbs/'.$employee->avatar);
                }
                $employee->avatar_name = $file->getClientOriginalName();
                $employee->avatar_type = $file->getClientOriginalExtension();
                $employee->avatar_size = $file->getSize();
                $employee->avatar=$this->upload_file($employee->condominium_id.'/employees/', $file);
            }
            $employee->name= $request->name;
            $employee->position= $request->position;
            $employee->NIT= $request->NIT;
            $employee->email= $request->email;
            $employee->phone= $request->phone;
            $employee->cell= $request->cell;
            $employee->address= $request->address;
            $employee->notes= $request->notes;
            $employee->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Empleado actualizado exitosamente',
                    'employee' => $employee
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
            $employee = Employee::find($id);
            if($employee->avatar){
                Storage::delete($employee->condominium_id.'/employees/'.$employee->avatar);
                Storage::delete($employee->condominium_id.'/employees/thumbs/'.$employee->avatar);
            }
            $employee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente'
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
            $employee = Employee::find($id);
            ($employee->active)?$employee->active=false:$employee->active=true;
            $employee->save();

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
    
    public function rpt_employees()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'employees' => $this->condominium->employees()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_employees', $data);
        
        return $pdf->stream('Empleados.pdf');

    }

}
