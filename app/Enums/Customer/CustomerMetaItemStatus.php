<?php


namespace App\Enums\Customer;


use App\Common\BaseEnum;

class CustomerMetaItemStatus extends BaseEnum
{
    const SUBMITTED = 0;
    const ACCEPTED = 1;
    const REJECTED = 2;
}
