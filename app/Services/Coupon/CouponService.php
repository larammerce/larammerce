<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\CustomerUser;
use Illuminate\Support\Collection;

class CouponService {

    /**
     * @param CustomerUser $customer_user
     * @return Collection|Coupon[]
     */
    public static function getNotUsedCoupons(CustomerUser $customer_user): Collection|array {
        return $customer_user->coupons()->where("used_at", null)
            ->get();
    }
}