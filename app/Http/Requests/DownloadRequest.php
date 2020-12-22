<?php

namespace App\Http\Requests;

class DownloadRequest extends APIFormRequest
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
            'package' => 'required|in:standard,extended,exclusive',
            'token' => 'required|exists:tokens,token'
        ];
    }
}