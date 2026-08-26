<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExtraChargeRequest  extends FormRequest
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
                $rules = [
                    'country_id'   => 'sometimes|required',
                    'city_id'      => 'sometimes|required',
                    'title'        => 'sometimes|required',
                    'charges'      => 'sometimes|required|numeric',
                ];

        return $rules;
    }

    public function messages()
    {
        return [
            'country_id.required'                   => 'You must select at least one country.',
            'city_id.required'                      => 'You must select at least one city.',
            'title.required'                        => 'The Title field is required.',
            'charges.required'                      => 'The Charges field is required.',

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
