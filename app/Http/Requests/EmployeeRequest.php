<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profesional;
use Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the technician is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */    
    public function rules()
    {       
        $rules = [];

        $condominium_id=$this->request->get('condominium_id');        
        $employee_id=$this->request->get('employee_id');
        if($employee_id>0){
            $rules['name'] = 'unique:employees,name,'.$employee_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'unique:employees,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'name.unique'  => 'El empleado ya ha sido registrado.'
        ];
    }

}
