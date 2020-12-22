<?php

namespace App\Http\Controllers\API\Auth;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeMessage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller {

    public function verify(Request $request, $id) {
        $user = User::findOrFail($id);

        if (!$request->hasValidSignature() || !$user)
            return redirect(env('APP_PUBLIC_URL').'?EVI=1');

        if (!$user->hasVerifiedEmail())
            $user->markEmailAsVerified();
//
//        if (!$user->last_login)
//            $user->sendWelcomeNotification();

        return redirect(env('APP_PUBLIC_URL').'?EV=1');
    }


    public function resend(Request $request) {
        if (!$request->has('email'))
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'Email is required');

        $user = User::where('email', $request->email)->first();
        if (!$user)
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'User not found');

        if ($user->email_verified_at)
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED, 'User is already verified');

        $user->sendEmailVerificationNotification();

        return respondOK();
    }

}
