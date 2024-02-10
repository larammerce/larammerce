<?php

namespace App\Interfaces\Repositories;

interface JobRepository
{
    public function countFailed(string $queue): int;

    public function count(string $queue): int;

    public function getQueueStatus(string $key): int;

    public function setQueueStatus(string $key, int $status): void;
}
