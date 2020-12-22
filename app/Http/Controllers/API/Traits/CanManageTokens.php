<?php

namespace App\Http\Controllers\API\Traits;

use App\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait CanManageTokens {

    public function generateToken() {
        $token = new Token();
        $token->user_id = Auth::guard('api')->id();
        $token->token = Str::random(20);
        $token->save();
        return $token;
    }

    public function authorizeToken($token) {
        $token = Token::where('token', $token)->first();
        if ($token) {
            $token->delete();
        }
        return $token;
    }

}
