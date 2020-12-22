<?php

namespace App\Http\Requests;

use App\Exceptions\APIValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class APIFormRequest extends FormRequest {

    protected function failedValidation(Validator $validator) {
        throw (new APIValidationException($validator))->errorBag($this->errorBag);
    }

}
