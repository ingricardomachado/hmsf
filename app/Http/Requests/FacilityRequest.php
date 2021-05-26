<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityRequest extends FormRequest
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
        $facility_id=$this->request->get('facility_id');

        if($facility_id>0){
            $rules['name'] = 'required|unique:facilities,name,'.$facility_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:facilities,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'La instalación ya existe.',
        ];
    }
}
