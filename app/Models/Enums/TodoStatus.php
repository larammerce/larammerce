<?php

namespace App\Models\Enums;

use App\Utils\Common\BaseEnum;

class TodoStatus extends BaseEnum
{
    const NEW_IN=0;
    const IN_PROGRESS=1;
    const READY_FOR_TEST=2;
    const DONE=3;
}
