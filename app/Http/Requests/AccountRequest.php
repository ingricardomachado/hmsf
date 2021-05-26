<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class AccountRequest extends FormRequest
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
        $car_id=$this->request->get('car_id');

        if($car_id>0){
            $rules['aliase'] = 'required|unique:accounts,aliase,'.$car_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['aliase'] = 'required|unique:accounts,aliase,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'aliase.unique' => 'El nombre de la cuenta ya existe.',
        ];
    }
}
