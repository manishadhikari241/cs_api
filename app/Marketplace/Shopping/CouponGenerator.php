<?php

namespace App\Marketplace\Shopping;

use Illuminate\Database\Eloquent\Model;

class CouponGenerator extends Model
{
    public static function make(array $request)
    {
        $vouchercode = $request['code'] ?? self::generateUniqueCode(6);
        return Coupon::forceCreate([
          'is_active'       => $request['is_active'],
          'name'            => $request['name'] ?? $vouchercode,
          'code'            => $vouchercode,
          'discount_type'   => $request['discount_type'],
          'amount'          => $request['amount'],
          'min_total'       => $request['min_total'] ?? null,
          'studio_id'       => $request['studio_id'] ?? null,
          'user_id'         => $request['user_id'] ?? null,
          'uses_per_coupon' => $request['uses_per_coupon'],
          'uses_per_user'   => $request['uses_per_user'],
          'start_date'      => $request['start_date'],
          'end_date'        => $request['end_date'],
      ]);
    }

    public static function generateUniqueCode($length)
    {
        $unique   = false;
        $attempts = 0;
        while (!$unique && $attempts < 100) {
            $attempts += 1;
            $code   = str_random($length);
            $unique = !Coupon::where('code', $code)->exists();
        }
        return $code;
    }
}
