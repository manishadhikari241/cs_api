<?php

namespace App\Http\Controllers\API\CMS;

use App\CSCoupon;
use App\General\UploadFile;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Quota;
use App\User;
use App\Utilities\Storage\S3;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller {

    public function index(Request $request) {
        $users = User::orderBy('id', 'desc');
        if ($request->term) {
            $users = $users->where('email', 'like', '%' . $request->term . '%')
                ->orWhere('first_name', 'like', '%' . $request->term . '%');
        }
        return $users->orderBy('id', 'desc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $user = User::findOrFail($id);
        return $user;
    }

    public function create(Request $request) {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = Carbon::now();
        $user->lang_pref = 'en';
        $user->save();
        return response()->json($user, 201);
    }

    public function toggleCreatorPrivileges(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->role_id = $user->role_id == 0 ? 2 : 0;
        $user->save();
        return $user;
    }

    public function updatePassword(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->algorithm = 'bcrypt';
        $user->password = Hash::make($request->new_password);
        $user->save();
        return respondOK();
    }

    public function updateAvatar(Request $request, $id) {
        $user = User::findOrFail($id);
        
        if ($user->avatar) {
            $fullPath = $user->getUploadPath().$user->avatar;
            try {
                $bucket = getenv('S3_BUCKET');
                $cloud  = S3::boot();
                $cloud->delete($fullPath);
            } catch (Exception $e) {}
        }

        (new UploadFile($request->file('image')))->to($user)->save('avatar');
        
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

    public function updateQuota(Request $request, $id) {
        $user = User::findOrFail($id);

        $coupon = new CSCoupon();
        $coupon->code = Str::random(10);
        $coupon->package = $request->pkg;
        $coupon->quantity = $request->quantity;
        $coupon->save();

        $coupon->activate($user);

        return respondOK();
    }

}

