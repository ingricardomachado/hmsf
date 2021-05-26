<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Facility;

class ReservationRequest extends FormRequest
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
        $facility=Facility::find($this->request->get('facility_id'));
        
        if (!$this->request->get('all_day')){
            $rules['start'] = 'required';
            $rules['end'] = 'required|gt:start';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'end.gt' => 'La hora final de reservación debe ser mayor a la hora inicial.',
        ];
    }
}
