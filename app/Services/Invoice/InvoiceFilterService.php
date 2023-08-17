<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceFilterService
{
    public static function getAllPaginated()
    {
        return Invoice::paginate(Invoice::getPaginationCount());
    }

    public static function getFilteredPaginated(Request $request)
    {
        return Invoice::where('customer_user_id', $request->customer_user_id)->paginate(Invoice::getPaginationCount());
    }
}