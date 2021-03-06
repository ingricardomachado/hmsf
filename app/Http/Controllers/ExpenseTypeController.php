<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ExpenseTypeRequest;
use App\Models\ExpenseType;
use App\Models\Property;
use App\Models\Setting;
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
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ExpenseTypeController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    
    
    /**
     * Display a listing of the expense_type.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('expense_types.index');
    }

    public function datatable()
    {        
        /*Se construye asi para que funcione el search de Yajra*/
        $expense_types = ExpenseType::orderBy('name');
        
        return Datatables::of($expense_types)
            ->addColumn('action', function ($expense_type) {
                    if($expense_type->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalExpenseType('.$expense_type->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$expense_type->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$expense_type->id.'`, `'.$expense_type->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$expense_type->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';
                    }
                })           
            ->editColumn('name', function ($expense_type) {                    
                    return '<a href="#"  onclick="showModalExpenseType('.$expense_type->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$expense_type->name.'</a>';
                })
            ->editColumn('status', function ($expense_type) {                    
                    return $expense_type->status_label;
                })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified expense_type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $expense_type = new ExpenseType();
        }else{
            $expense_type = ExpenseType::find($id);
        }
        
        return view('expense_types.save')->with('expense_type', $expense_type);
    }

    /**
     * Store a newly created expense_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseTypeRequest $request)
    {
        try {
            $expense_type = new ExpenseType();
            $expense_type->name=$request->name;
            $expense_type->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de gasto registrado exitosamente',
                    'expense_type' => $expense_type->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified expense_type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseTypeRequest $request, $id)
    {        
        try {
            $expense_type = ExpenseType::find($id);
            $expense_type->name=$request->name;
            $expense_type->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Tipo de gasto actualizado exitosamente',
                    'expense_type' => $expense_type
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified expense_type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $expense_type = ExpenseType::find($id);
            $expense_type->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de gasto eliminado exitosamente'
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
            $expense_type = ExpenseType::find($id);
            ($expense_type->active)?$expense_type->active=false:$expense_type->active=true;
            $expense_type->save();

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
    
    public function rpt_expense_types()
    {        
        $setting=Setting::first();
        $logo=($setting->logo)?'data:image/png;base64, '.base64_encode(Storage::get('settings/'.$setting->logo)):'';

        $expense_types=ExpenseType::orderBy('name')->get();
        
        $data=[
            'company' => $setting->company,
            'expense_types' => $expense_types,
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_expense_types', $data);
        
        return $pdf->stream('Tipos de Gastos.pdf');

    }
}
