<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:45 PM
 */

namespace App\Enums\Invoice;


use App\Common\BaseEnum;

class ShipmentStatus extends BaseEnum
{
    const WAITING_TO_CONFIRM = 0;
    const PREPARING_TO_SEND = 1;
    const WAREHOUSE_EXIT_TAB = 2;
    const SENDING = 3;
    const DELIVERED = 4;
}
