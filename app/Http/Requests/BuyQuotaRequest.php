<?php

namespace App\Http\Requests;

class BuyQuotaRequest extends APIFormRequest
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
            'nonce' => 'required',
            'package' => 'required|in:standard,extended,exclusive,simulator',
            'package_type' => 'required|in:min,max',
            'amount' => 'required|integer'
        ];
    }
}
