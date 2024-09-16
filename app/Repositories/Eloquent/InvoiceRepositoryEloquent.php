<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\Repositories\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InvoiceRepositoryEloquent implements InvoiceRepositoryInterface
{
    public function findByQueryAndFilters(Builder $builder, Request $request): Builder
    {
        return $builder
            ->findInDateRange(
                $request->input("create_date_from"),
                $request->input("create_date_to"))
            ->findInPaymentDateRange(
                $request->input("payment_date_from"),
                $request->input("payment_date_to"))
            ->findInPriceRange(
                $request->input("price_from"),
                $request->input("price_to"))
            ->findByFullName(
                $request->input("first_name"),
                $request->input("last_name"))
            ->findByNationalCode(
                $request->input("national_code"))
            ->findByStatus(
                $request->input("payment_status"))
            ->findByCustomerNumber(
                $request->input("user_number"))
                ;
    }
}
