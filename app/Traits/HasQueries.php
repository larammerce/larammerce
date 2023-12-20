<?php

namespace App\Traits;

use App\Enums\Invoice\PaymentStatus;
use App\Models\CustomerUser;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait HasQueries
{
    public function scopeFindInPaymentDateRange($query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, function ($query) use ($from) {
                $query->where(function ($q) use ($from,) {
                    $q->where('created_at', '>=', $from)
                        ->orWhere('updated_at', '>=', $from);
                })
                    ->where('payment_status', PaymentStatus::PAID_OUT);
            })->when($to, function ($query) use ($to) {
                $query->where(function ($q) use ($to,) {
                    $q->where('created_at', '<=', $to)
                        ->orWhere('updated_at', '<=', $to);
                })
                    ->where('payment_status', PaymentStatus::PAID_OUT);
            });
    }

    public function scopeFindInDateRange($query, ?string $from, ?string $to): Builder
    {
        return $query->when($from, function ($query) use ($from) {
            $query->where('created_at', '>=', $from);
        })->when($to, function ($query) use ($to) {
            $query->where('created_at', '<=', $to);
        });
    }

    public function scopeFindInPriceRange($query, ?int $from, ?int $to): Builder
    {
        return $query->when($from, function ($q) use ($from) {
            $q->where('sum', '>=', $from);
        })->when($to, function ($q) use ($to) {
            $q->where('sum', '<=', $to);
        });
    }

    public function scopeFindByStatus($query, ?int $status): Builder
    {
        return $query->when($status, function ($q) use ($status) {
            $q->where('payment_status', $status);
        });
    }

    public function scopeFindByFullName($query, ?string $name,?string $lastName): Builder
    {
        if (!$name || !$lastName) return $query;
        $user = User::where('name', $name)
            ->where('family',$lastName)
            ->where('is_customer_user',1)
            ->first();
        if (!$user) return $query->whereNull('id');
        return $query->where('customer_user_id', $user->customerUser->id);
    }

    public function scopeFindByNationalCode($query, ?string $code): Builder
    {
        if (!$code) return $query;
        $user = CustomerUser::where('national_code', $code)->first();
        if (!$user) return $query->whereNull('id');
        return $query->where('customer_user_id', $user->id);
    }

    public function scopeFindByCustomerNumber($query, ?string $customerNumber): Builder
    {
        return $query->when($customerNumber, function ($q) use ($customerNumber) {
            $q->where('phone_number', $customerNumber);
        });
    }
}
