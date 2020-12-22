<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Marketplace\Common\Country;
use App\Payment;
use App\Quota;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {

    public function update(UpdateUserRequest $request, $id) {
        $user = User::findOrFail($id);

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        if ($request->mobile) {
            $mobile = preg_replace('/\s+/', '', $request->mobile);
            $user->mobile = $request->mobileCode . ' ' . $mobile;
            $user->country_id = Country::where('iso2', $request->country)->first()->id;
        }
        $user->company = $request->company ?? $user->company;
        $user->industry = $request->industry ?? $user->industry;
        $user->lang_pref = $request->lang_pref ?? $user->lang_pref;

        $email_updated = false;
        if ($request->email && $request->email != $user->email) {
            if (User::where('email', $request->email)->exists())
                return respondError(ErrorCodes::DUPLICATE_ENTRY, Response::HTTP_CONFLICT, 'Email already exists');

            $user->email = $request->email;
            $user->email_verified_at = null;
            $email_updated = true;
            $user->sendEmailVerificationNotification();
        }

        $user->save();
        return ['email_updated' => $email_updated];
    }

    public function updatePassword(UpdateUserPasswordRequest $request, $id) {
        $user = User::findOrFail($id);

        if (Hash::check($request->old_password, $user->password)) {
            $user->algorithm = 'bcrypt';
            $user->password = Hash::make($request->new_password);
        } else {
            return respondError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED, 'Incorrect password');
        }

        $user->save();
        return respondOK();
    }

    public function showQuota(Request $request, $id) {
        $user = User::findOrFail($id);
        $quota = $user->quota;
        if (!$quota) {
            $quota = Quota::createEmpty($id);
            $quota->save();
        }
        return $quota;
    }

    public function showPayments(Request $request, $id) {
        return Payment::where('user_id', $id)->orderBy('id', 'desc')->get();
    }

    public function lang_pref(Request $request) {
        $user = Auth::guard('api')->user();
        $user->lang_pref = $request->lang;
        $user->save();
        return response()->json($user, 201);
    }

}

