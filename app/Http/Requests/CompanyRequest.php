<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class CompanyRequest extends FormRequest
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

        $company_id=$this->request->get('company_id');

        if($company_id>0){
            $rules['name'] = 'required|unique:companies,name,'.$company_id;
        }else{
            $rules['name'] = 'required|unique:companies,name,NULL,id';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'La empresa ya existe.',
        ];
    }
}
