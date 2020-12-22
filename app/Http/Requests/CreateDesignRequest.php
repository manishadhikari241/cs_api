<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class CreateDesignRequest extends FormRequest
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
            'tags'=>'required|min:9',
        ];
    }
    public function messages()
    {
        return[
            'tags.min'=>'Please Select at least 5 tags'
        ];
    }

}
