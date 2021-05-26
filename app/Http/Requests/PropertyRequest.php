<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
        $property_id=$this->request->get('property_id');
        if($property_id>0){
            $rules['number'] = 'required|unique:properties,number,'.$property_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['number'] = 'required|unique:properties,number,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'number.unique' => 'El Número de la propiedad ya existe.',
        ];
    }
}
