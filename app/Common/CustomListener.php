<?php

namespace App\Common;

use App\Enums\Queue\QueueStatus;
use App\Services\Queue\QueueService;
use Illuminate\Queue\Listener;
use Illuminate\Queue\ListenerOptions;

class CustomListener extends Listener
{
    public function __construct($commandPath, protected QueueService $queueService)
    {
        parent::__construct($commandPath);
    }

    public function listen($connection, $queue, ListenerOptions $options)
    {
        $process = $this->makeProcess($connection, $queue, $options);

        while (true) {
            if ($this->queueService->getStatus($queue)[0] == QueueStatus::STOPPED)
            {
                sleep(1);
                continue;
            }
            $this->runProcess($process, $options->memory);
        }
    }
}
