<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/12/17
 * Time: 3:45 PM
 */

namespace App\Models\Enums;


use App\Utils\Common\BaseEnum;

class ShipmentMethod extends BaseEnum
{
    const NONE = -1;
    const EXPRESS = 0;
    const POST = 1;
}