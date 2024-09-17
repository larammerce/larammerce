<?php

namespace App\Enums\Queue;

use App\Common\BaseEnum;

class QueueDispatchType extends BaseEnum
{
    const AUTOMATIC = 0;
    const MANUAL = 1;
}
