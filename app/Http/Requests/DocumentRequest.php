<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DocumentType;

class DocumentRequest extends FormRequest
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

        $document_type=DocumentType::find($this->request->get('document_type'));
        $document_id=$this->request->get('document_id');
        $condominium_id=$document_type->condominium_id;

        if($document_id>0){
            $rules['name'] = 'required|unique:documents,name,'.$document_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:documents,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El nombre del documento ya existe.',
        ];
    }
}
