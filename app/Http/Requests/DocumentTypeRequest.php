<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class DocumentTypeRequest extends FormRequest
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
        $document_type_id=$this->request->get('document_type_id');

        if($document_type_id>0){
            $rules['name'] = 'required|unique:document_types,name,'.$document_type_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:document_types,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'La clasificación ya existe.',
        ];
    }
}
