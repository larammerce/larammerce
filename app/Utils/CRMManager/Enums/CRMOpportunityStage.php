<?php

namespace App\Utils\CRMManager\Enums;

use App\Common\BaseEnum;

class CRMOpportunityStage extends BaseEnum {
    const CART_IN_PROGRESS = 0;
    const INVOICE_CREATED = 1;
    const PAYMENT_DONE = 2;
    const DELIVERY_DONE = 3;
    const CLOSED = 4;
}
