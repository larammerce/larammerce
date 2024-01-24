<?php

namespace App\Interfaces\Repositories;

interface JobRepository
{
    public function countFailed(string $queue): int;

    public function count(string $queue): int;

    public function getJobs(string $queue): array;

    public function pushJobs(string $key, array $data): void;

    public function deleteKey(string $key): void;

    public function checkIfKeyExists(string $key): bool;

    public function deleteJobs(string $key): void;

    public function getJobsAndDeleteKey(string $key): array;

    public function getQueueStatus(string $key): int;

    public function setQueueStatus(string $key, int $status): void;
}
