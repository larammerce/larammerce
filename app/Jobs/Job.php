<?php

namespace App\Jobs;

use App\Enums\Queue\QueueDispatchType;
use App\Enums\Queue\QueuePriority;
use Illuminate\Bus\Queueable;

abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "onQueue" and "delay" queue helper methods.
    |
    */

    use Queueable;

    abstract public function getDispatchType(): ?int;

    abstract public function getQueuePriority(): ?int;
}
