<?php

namespace App\Http\Requests;

class AuthRegisterRequest extends APIFormRequest
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
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'country' => 'required|exists:country,iso2',
            'mobileCode' => 'required',
            'mobile' => 'required|string',
            'password' => 'required|string|min:8',
            'coupon' => 'string|nullable',
            'lang' => 'required|string'
        ];
    }
}
