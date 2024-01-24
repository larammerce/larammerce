<?php

namespace App\Helpers;

class QueueHelper
{
    public static function getBufferKey($queue): string
    {
        return 'queues:' . $queue . ':buffer';
    }

    public static function getQueueKey($queue): string
    {
        return 'queues:' . $queue;
    }

    public static function getStatusKey($queue): string
    {
        return 'queues:' . $queue . ':status';
    }
}
