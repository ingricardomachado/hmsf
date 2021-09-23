<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Partner;
use Session;

class PartnerRequest extends FormRequest
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

        $partner_id=$this->request->get('partner_id');
        if($partner_id>0){
            $partner=Partner::findOrFail($partner_id);
            $rules['email'] = 'email|max:50|unique:users,email,'.$partner->user_id;
            if ($this->request->get('change_password')){        
                $rules['password'] = 'min:6|max:15|required_with:password_confirmation|string|confirmed';
            }
        }else{
            $rules['email'] = 'email|max:50|unique:users';
            $rules['password'] = 'min:6|max:15|required_with:password_confirmation|string|confirmed';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'email.unique' => 'El correo ya ha sido registrado',
          'password.confirmed' => 'La confirmación de la contraseña no coincide',
        ];
    }
}
