<?php

namespace App\Http\Controllers\Admin\Api\V1;

use App\Models\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\User;
use App\Utils\Common\MessageFactory;
use App\Utils\Jalali\JDateTime;
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

        return MessageFactory::jsonResponse([], 200, compact("amount"));
    }

    public function getMonthlySalesAmount(): \Illuminate\Http\JsonResponse
    {
        $now = Carbon::now();
        list($year, $month, $day) = JDateTime::toJalali($now->year, $now->month, $now->day);
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
        list($start_year, $start_month, $start_day) = JDateTime::toGregorian($year, $month, 1);
        list($end_year, $end_month, $end_day) = JDateTime::toGregorian($year, $month, $j_days_in_month[$month - 1]);

        $amount = Invoice::whereIn("payment_status", [PaymentStatus::SUBMITTED, PaymentStatus::CONFIRMED, PaymentStatus::PAID_OUT])
            ->where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
            ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
            ->sum("sum");

        return MessageFactory::jsonResponse([], 200, compact("amount"));
    }

    public function getYearlySalesAmount(): \Illuminate\Http\JsonResponse
    {
        $now = Carbon::now();
        list($year, $month, $day) = JDateTime::toJalali($now->year, $now->month, $now->day);
        list($start_year, $start_month, $start_day) = JDateTime::toGregorian($year, 1, 1);
        list($end_year, $end_month, $end_day) = JDateTime::toGregorian($year, 12, 29);

        $amount = Invoice::whereIn("payment_status", [PaymentStatus::SUBMITTED, PaymentStatus::CONFIRMED, PaymentStatus::PAID_OUT])
            ->where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
            ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
            ->sum("sum");

        return MessageFactory::jsonResponse([], 200, compact("amount"));
    }

    public function getPreviousYearSalesAmount(): \Illuminate\Http\JsonResponse
    {
        $now = Carbon::now();
        list($year, $month, $day) = JDateTime::toJalali($now->year, $now->month, $now->day);
        list($start_year, $start_month, $start_day) = JDateTime::toGregorian($year - 1, 1, 1);
        list($end_year, $end_month, $end_day) = JDateTime::toGregorian($year - 1, 12, 29);

        $amount = Invoice::whereIn("payment_status", [PaymentStatus::SUBMITTED, PaymentStatus::CONFIRMED, PaymentStatus::PAID_OUT])
            ->where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
            ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
            ->sum("sum");

        return MessageFactory::jsonResponse([], 200, compact("amount"));
    }

    public function getOverallBarChartData()
    {
        $now = Carbon::now();
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
        list($year, $month, $day) = JDateTime::toJalali($now->year, $now->month, $now->day);
        $labels = [];
        $datasets = [
            [
                "label" => "ثبت نام",
                "backgroundColor" => "rgb(255, 87, 87)",
                "borderColor" => "rgb(110, 11, 4)",
                "data" => []
            ],
            [
                "label" => "ثبت سفارش",
                "backgroundColor" => "rgb(84, 247, 103)",
                "borderColor" => "rgb(39, 92, 11)",
                "data" => []
            ],
            [
                "label" => "سفارش نهایی شده",
                "backgroundColor" => "rgb(93, 99, 252)",
                "borderColor" => "rgb(11, 21, 82)",
                "data" => []
            ]
        ];
        for ($i = 1; $i <= 12; $i++) {
            $tmp_month = $month + $i;
            $carry = (int)($tmp_month / 12);
            $tmp_month = $tmp_month % 12;
            $tmp_year = ($year - 1 + $carry);

            if ($tmp_month === 0) {
                $tmp_month = 12;
                $tmp_year -= 1;
            }

            $labels[] = JDateTime::getMonthNames($tmp_month) . " " . JDateTime::convertNumbers("$tmp_year");

            list($start_year, $start_month, $start_day) = JDateTime::toGregorian($tmp_year, $tmp_month, 1);
            list($end_year, $end_month, $end_day) = JDateTime::toGregorian($tmp_year, $tmp_month, $j_days_in_month[$tmp_month - 1]);

            $datasets[0]["data"][] = User::where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
                ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
                ->count();
            $datasets[1]["data"][] = Invoice::where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
                ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
                ->count();

            $datasets[2]["data"][] = Invoice::whereIn("payment_status", [PaymentStatus::SUBMITTED, PaymentStatus::CONFIRMED, PaymentStatus::PAID_OUT])
                ->where("created_at", ">=", $now->format("$start_year-$start_month-$start_day 00:00:00"))
                ->where("created_at", "<=", $now->format("$end_year-$end_month-$end_day 23:59:59"))
                ->count();
        }

        return MessageFactory::jsonResponse([], 200, compact("labels", "datasets"));
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

    public function getModel(): ?string
    {
        return null;
    }
}
