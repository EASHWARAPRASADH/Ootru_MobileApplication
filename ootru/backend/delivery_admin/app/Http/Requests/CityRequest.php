<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CityRequest extends FormRequest
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
        $city = $this->route('city') ?? $this->id;
        $rules = [
            'name'                  => 'sometimes|required|unique:cities,name,' . $city,
            'country_id'            => 'sometimes|required',
            'fixed_charges'         => 'sometimes|required',
            'cancel_charges'        => 'sometimes|required',
            'min_distance'          => 'sometimes|required',
            'min_weight'            => 'sometimes|required',
            'per_distance_charges'  => 'sometimes|required',
            'per_weight_charges'    => 'sometimes|required',
            'admin_commission'      => 'sometimes|required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'country_id.required'                   => 'You must select at least one country.',
            'name.required'                         => 'The name field is required.',
            'fixed_charges.required'                => 'The Fixed Chareges required',
            'cancel_charges.required'               => 'The Cancel Chareges required',
            'min_distance.required'                 => 'The Min Chareges required',
            'min_weight.required'                   => 'The Min Weight required',
            'per_distance_charges.required'         => 'The Per Distance Chareges required',
            'per_weight_charges.required'           => 'The Per Weight Chareges required',
            'admin_commission.required'             => 'The Admin commision required',

        ];
    }
    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => 422,
            'message' => $validator->errors()->first(),
        ];

        if ( request()->is('api*')){
           throw new HttpResponseException( response()->json($data,422) );
        }

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
