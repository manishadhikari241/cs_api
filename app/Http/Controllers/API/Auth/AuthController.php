<?php

namespace App\Http\Controllers\API\Auth;

use App\Constants\ErrorCodes;
use App\CSCoupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Marketplace\Common\Country;
use App\Newsletter;
use App\Quota;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {

    public function login(AuthLoginRequest $request) {
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED, 'This email is not registered');

        if ($user && Hash::check($request->password, $user->password) && !$user->email_verified_at)
            return respondError(ErrorCodes::UNVERIFIED, Response::HTTP_UNAUTHORIZED, 'Please, confirm your email');

        if (!$token = Auth::guard('api')->attempt(request(['email', 'password'])))
            return respondError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED);

        $user->last_login = Carbon::now();
        $user->save();

        if (!$user->quota) {
            $quota = Quota::createEmpty($user->id);
            $quota->save();
        }

        return $this->respondWithToken($token);
    }

    public function register(AuthRegisterRequest $request) {
        if (User::where('email', $request->email)->exists())
            return respondError(ErrorCodes::DUPLICATE_ENTRY, Response::HTTP_CONFLICT, 'Email already exists');

        $user = new User(request(['first_name', 'last_name', 'email']));
        $mobile = preg_replace('/\s+/', '', $request->mobile);
        $user->mobile = $request->mobileCode . ' ' . $mobile;
        $user->role_id = 0;
        $user->password = bcrypt($request->password);
        $user->lang_pref = ($request->lang == 'ch' ? 'zh-CN' : 'en');
        $user->country_id = Country::where('iso2', $request->country)->first()->id;
        $user->save();

        $quota = Quota::createEmpty($user->id);
        $quota->save();

        if ($request->coupon) {
            $coupon = CSCoupon::where('code', $request->coupon)->first();
            if ($coupon)
                $coupon->activate($user);
        }

        if ($request->newsletter) {
            $newsletter = new Newsletter();
            $newsletter->user_id = $user->id;
            $newsletter->email = $user->email;
            $newsletter->save();
        }

        $user->sendEmailVerificationNotification();

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function me() {
        $user = Auth::user();
        $country = Country::find($user->country_id);
        $user->country = $country->iso2;
        return response()->json(Auth::user());
    }

    public function logout(){
        Auth::guard('api')->logout();
        return respondOK();
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => env('JWT_TTL')
        ]);
    }

}
