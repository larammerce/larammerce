<?php

namespace App\Services\DiscountGroup;

use App\Models\DiscountGroup;

class DiscountGroupService
{
    public static function getAllPaginated()
    {
        return DiscountGroup::paginate(DiscountGroup::getPaginationCount());
    }

    public static function getDeletedPaginated()
    {
        return DiscountGroup::onlyTrashed()->paginate(DiscountGroup::getPaginationCount());
    }
}