<?php

namespace App\Http\Controllers\API\Auth;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller {

    public function sendResetLinkEmail(SendPasswordResetRequest $request) {
        $response = PasswordBroker::RESET_LINK_SENT;
        if (User::where('email', $request->email)->exists())
            $response = Password::broker()->sendResetLink(['email' => $request->email]);
        else
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'This email is not registered');
        if ($response == PasswordBroker::RESET_LINK_SENT)
            return respondOK();
        else if ($response === PasswordBroker::RESET_THROTTLED)
            return respondError(ErrorCodes::THROTTLED, Response::HTTP_TOO_MANY_REQUESTS, 'Please, try again in couple of minutes');
        return respondError(ErrorCodes::UNKNOWN_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR, 'Unknown Error');
    }

    public function showReset(Request $request) {
        return redirect(env('APP_PUBLIC_URL').'?PR=1&token='.$request->token.'&email='.$request->email);
    }

    public function reset(ResetPasswordRequest $request) {
        $response = Password::broker()->reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']), function ($user, $password) {
                $user->password = bcrypt($password);
                $user->algorithm = 'bcrypt';
                $user->save();
            }
        );
        if ($response === PasswordBroker::PASSWORD_RESET)
            return respondOK();
        return respondError(ErrorCodes::UNKNOWN_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR, 'Unknown Error');
    }

}
