<?php

namespace App\Http\Requests;

use App\Service\Facades\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
//            'user_id' => 'required',
            'client_email' => 'email',
            'client_name' => 'required',
            'client_signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the size and mime types as needed
            'status' => 'required',
//            'content' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => trans('validation.required'),
            'user_id.integer' => trans('validation.integer'),
            'client_email.required' => trans('validation.required'),
            'client_email.email' => trans('validation.email'),
            'client_name.required' => trans('validation.required'),
            'client_signature.required' => trans('validation.required'),
            'status.required' => trans('validation.required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Api::error($validator->errors()->first(), 422));
    }

}
