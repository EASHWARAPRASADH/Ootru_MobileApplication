<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VehicleRequest  extends FormRequest
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
        $vehicle = $this->route('vehicle');
        $rules = [
            'title'       => 'sometimes|required',
            'capacity'    => 'sometimes|required',
            'size'        => 'sometimes|required',
            'description' => 'sometimes|required',
            'vehicle_image' => 'sometimes|required',

        ];
        return $rules;
    }

    public function messages()
    {
        return [

            'title.required'         => 'The Title field is required.',
            'capacity.required'      => 'The Capacity field is required.',
            'size.required'          => 'The Size field is required.',
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
