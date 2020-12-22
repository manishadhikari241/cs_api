<?php

namespace App\Marketplace\Shopping;

use Carbon\Carbon;

class ManageCoupon
{
    public function syncWithPromotion(Coupon $coupon, $data=[])
    {
        $coupon->update([
            'start_date' => $data['started_at'],
            'end_date' => $data['expired_at'],
            'is_active' => $data['is_active'],
        ]);
        return $coupon;
    }
}