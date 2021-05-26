<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class ExpenseTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        $expense_type_id=$this->request->get('expense_type_id');

        if($expense_type_id>0){
            $rules['name'] = 'required|unique:expense_types,name,'.$expense_type_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:expense_types,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El tipo de egreso ya existe.',
        ];
    }
}
