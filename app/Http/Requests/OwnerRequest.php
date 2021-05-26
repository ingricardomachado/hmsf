<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class OwnerRequest extends FormRequest
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

        $owner_id= $this->request->get('owner_id');
        if($owner_id>0){
          $rules['email'] = 'email|max:50|unique:users,email,'.$owner_id;
        }else{
          $rules['email'] = 'email|max:50|unique:users';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'email.unique' => 'El correo ya ha sido registrado',
        ];
    }
}
