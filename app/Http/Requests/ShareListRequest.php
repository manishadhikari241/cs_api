<?php

namespace App\Http\Requests;

class ShareListRequest extends APIFormRequest
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
            'emails' => 'required|array',
            'emails.*' => 'email',
            'name' => 'required|string'
        ];
    }
}
