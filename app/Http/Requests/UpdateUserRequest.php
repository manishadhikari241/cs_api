<?php

namespace App\Http\Requests;

class UpdateUserRequest extends APIFormRequest
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
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'country' => 'required|exists:country,iso2',
            'mobileCode' => 'required',
            'mobile' => 'string|nullable',
            'company' => 'string|nullable',
            'industry' => 'string|nullable',
            'lang_pref' => 'in:en,zh-CN|nullable',
            'email' => 'email|nullable'
        ];
    }
}
