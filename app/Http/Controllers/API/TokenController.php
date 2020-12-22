<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Traits\CanManageTokens;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller {

    use CanManageTokens;

    public function generate(Request $request) {
        return $this->generateToken();
    }

}
