<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeliveryPartnerRequest  extends FormRequest
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
        $method = strtolower($this->method());
        switch ($method) {
            case 'post':
                $rules = [
                    'title'                  => 'required|max:100|',
                    'subtitle'              => 'required|max:100|',
                    // 'frontend_data_image'                  => 'required|dimensions:min_width=599,min_height=835,max_width=599,max_height=835',

                ];
                break;
            case 'patch':
                $rules = [
                    'title'                  => 'required|max:100|',
                    'subtitle'                => 'required|max:100|',
                ];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required'               => 'The Title field is required.',
            'subtitle.required'            => 'The Subtitle field is required.',

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
        
        $fields = ['title','subtitle'];
        $required_field = array_combine($fields, $fields);

        if ( request()->is('api*')){
           throw new HttpResponseException( response()->json($data,422) );
        }

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json(['status' => false, 'validation_status' => 'jquery_validation', 'all_message' => $validator->errors(), 'event' => 'validation', 'required_field' => $required_field, 'message' => $validator->errors()->first()]));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
