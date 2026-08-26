<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeliveryManSectionRequest extends FormRequest
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
            'sections.0.title' => 'required|max:255',
        ];

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        $messages = [
            'sections.0.title.required' =>  __('message.request_required',['name'=>__('message.section_title')]),
            'sections.0.title.max' => __('message.max_characters', ['name' => __('message.section_title'), 'max' => 255]),
        ];
        
        return $messages;
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
