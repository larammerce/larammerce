<?php

namespace App\Http\Controllers\Admin\Api\V1;

use Illuminate\Support\Carbon;

class LiveReportsController extends BaseController
{
    public function getDailySalesAmount()
    {
        $now = Carbon::today("00:00");
        dd($now->format("Y/m/d H:i:s"));
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
