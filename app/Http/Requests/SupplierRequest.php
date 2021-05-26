<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SupplierCategory;

class SupplierRequest extends FormRequest
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

        $supplier_category=SupplierCategory::find($this->request->get('supplier_category'));
        $supplier_id=$this->request->get('supplier_id');
        $condominium_id=$supplier_category->condominium_id;

        if($supplier_id>0){
            $rules['name'] = 'required|unique:suppliers,name,'.$supplier_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['name'] = 'required|unique:suppliers,name,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'name.unique' => 'El Proveedor ya existe.',
        ];
    }
}
