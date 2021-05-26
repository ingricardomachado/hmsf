<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class SupplierCategoryRequest extends FormRequest
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
        $supplier_category_id=$this->request->get('supplier_category_id');

        if($supplier_category_id>0){
            $rules['name'] = 'required|unique:supplier_categories,name,'.$supplier_category_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:supplier_categories,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'La categoría ya existe.',
        ];
    }
}
