<?php

namespace App\Helpers;

class QueueHelper
{
    const prefix = 'queues:';
    public static function getBufferKey($queue): string
    {
        return static::prefix . $queue . ':buffer';
    }

    public static function getQueueKey($queue): string
    {
        return  static::prefix . $queue;
    }

    public static function getStatusKey($queue): string
    {
        return static::prefix . $queue . ':status';
    }
}
