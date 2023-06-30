<?php

namespace App\Http\Controllers\Customer;

class CouponController extends BaseController {
    public function index() {
        return h_view('public.coupons',
            [
                "coupons" => get_customer_user()->coupons()->orderBy("used_at", "desc")->get()
            ]);
    }
}