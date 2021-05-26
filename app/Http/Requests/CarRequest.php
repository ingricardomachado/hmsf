<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;

class CarRequest extends FormRequest
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

        $property=Property::find($this->request->get('property'));
        $car_id=$this->request->get('car_id');
        $condominium_id=$property->condominium_id;

        if($car_id>0){
            $rules['plate'] = 'required|unique:cars,plate,'.$car_id.',id,condominium_id,'.$condominium_id;
        }else{
            $rules['plate'] = 'required|unique:cars,plate,NULL,id,condominium_id,'.$condominium_id;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
          'plate.unique' => 'La placa ya existe.',
        ];
    }
}
