<?php

namespace App\Enums\Queue;

use App\Common\BaseEnum;

class QueuePriority extends BaseEnum
{
    const DEFAULT = 0;
    const LOW = 1;
    const HIGH = 2;
}
