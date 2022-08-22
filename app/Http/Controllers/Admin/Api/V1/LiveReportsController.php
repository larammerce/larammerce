<?php

namespace App\Http\Controllers\Admin\Api\V1;

use App\Models\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Utils\Common\MessageFactory;
use Illuminate\Support\Carbon;

class LiveReportsController extends BaseController
{
    public function getDailySalesAmount(): \Illuminate\Http\JsonResponse
    {
        $now = Carbon::now();
        $amount = Invoice::whereIn("payment_status", [PaymentStatus::SUBMITTED, PaymentStatus::CONFIRMED, PaymentStatus::PAID_OUT])
            ->where("created_at", ">=", $now->format("Y-m-d 00:00:00"))
            ->where("created_at", "<=", $now->format("Y-m-d 23:59:59"))
            ->sum("sum");
        return MessageFactory::jsonResponse([], 200, compact($amount));
    }

    public function getMonthlySalesAmount()
    {

    }

    public function getYearlySalesAmount()
    {

    }

    public function getPreviousYearSalesAmount()
    {

    }

    public function getHighChartProducts()
    {

    }

    public function getLowChartProducts()
    {

    }

    public function getLatestRegisteredCustomers()
    {

    }

    public function getLatestSubmittedOrders()
    {

    }

    public function getHighChartCustomers()
    {

    }

    public function getSalesChartData()
    {

    }

    public function getModel(): ?string
    {
        return null;
    }
}
