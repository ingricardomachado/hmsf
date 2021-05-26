<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Profesional;
use Auth;
use Illuminate\Support\Facades\Hash;

class ContactRequest extends FormRequest
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
        $contact_id=$this->request->get('contact_id');
        if($contact_id>0){
            $rules['name'] = 'unique:contacts,name,'.$contact_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'unique:contacts,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'name.unique'  => 'El contacto ya ha sido registrado.'
        ];
    }

}
