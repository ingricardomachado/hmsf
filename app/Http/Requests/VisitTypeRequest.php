<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class VisitTypeRequest extends FormRequest
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
        $visit_type_id=$this->request->get('visit_type_id');

        if($visit_type_id>0){
            $rules['name'] = 'required|unique:visit_types,name,'.$visit_type_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:visit_types,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El tipo de visita ya existe.',
        ];
    }
}
