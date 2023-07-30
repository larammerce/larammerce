<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:41 PM
 */

namespace App\Enums\Invoice;


use App\Common\BaseEnum;

class NewInvoiceType extends BaseEnum
{
    const CART_SUBMISSION = 0;
    const SHIPMENT = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_DONE = 3;
}
