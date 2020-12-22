<?php

namespace App\Http\Controllers\API\CMS;

use App\CSCoupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CouponsController extends Controller {

    public function index(Request $request) {
        $coupons = CSCoupon::orderBy('id', 'desc');
        if ($request->term)
            $coupons->where('package', $request->term);
        return $coupons->paginate(20);
    }

    public function show(Request $request, $id) {
        $coupon = CSCoupon::findOrFail($id);
        $coupon->translations;
        return $coupon;
    }

    public function create(Request $request) {
        $coupons = [];
        for ($i = 0; $i < 10; $i++) {
            $coupon = new CSCoupon();
            $coupon->code = Str::random(10);
            $coupon->package = $request->pkg;
            $coupon->quantity = 10;
            $coupon->save();
            array_push($coupons, $coupon);
        }

        return response()->json($coupons, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $coupon = CSCoupon::findOrFail($id);

        $coupon->code = $request->code;
        $coupon->package = $request->pkg;
        $coupon->quantity = $request->quantity;
        $coupon->save();

        return $coupon;
    }

    public function delete(Request $request, $id) {
        $coupon = CSCoupon::findOrFail($id);
        $coupon->delete();

        return respondOK();
    }

}
