<?php

namespace App\Http\Requests;

use App\Service\Facades\Api;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
//            'user_name' => 'unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:12',
        ];
    }

    public function messages()
    {
        return [
//            'user_name.required' => trans('validation.required'),
//            'user_name.unique' => trans('validation.unique'),
            'email.required' => trans('validation.required'),
            'email.email' => trans('validation.email'),
            'password.required' => trans('validation.required'),
            'password.min' => trans('validation.min'),
            'password.max' => trans('validation.max'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Api::error($validator->errors()->first(), 422));
    }
}
