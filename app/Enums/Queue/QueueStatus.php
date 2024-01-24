<?php

namespace App\Enums\Queue;

use \App\Common\BaseEnum;

class QueueStatus extends BaseEnum
{
    const RUNNING = 0;
    const STOPPED = 1;
}
