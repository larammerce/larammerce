<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:43 PM
 */

namespace App\Enums\Invoice;


use App\Common\BaseEnum;

class PaymentStatus extends BaseEnum
{
    // source: https://support.gocardless.com/hc/en-us/articles/213146225-Overview-of-payment-statuses
    const PENDING = 0; // invoice is created but no payment is submitted
    const SUBMITTED = 1; // online - bank accepted the payment
    const CONFIRMED = 2; // accounting accepted the payment
    const PAID_OUT = 3; // cash - customer payed cash to the delivery
    const FAILED = 4; // online - bank transaction failed
    const CANCELED = 5; // online - customer canceled the payment
    const CHARGED_BACK = 6; // money is payed back
}
