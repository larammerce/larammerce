<?php

namespace App\Services\Invoice;

use App\Models\Invoice;

class InvoiceFilterService
{
    public static function getAllPaginated()
    {
        return Invoice::paginate(Invoice::getPaginationCount());
    }

    public static function getFilteredPaginated()
    {
        return Invoice::where('customer_user_id', '33')->paginate(Invoice::getPaginationCount());
    }
}