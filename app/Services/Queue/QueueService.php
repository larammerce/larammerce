<?php

namespace App\Services\Queue;

use App\Enums\Queue\QueueStatus;
use App\Helpers\QueueHelper;
use App\Interfaces\Repositories\JobRepository;

class QueueService
{
    public function __construct(
        protected JobRepository $jobs
    )
    {
    }

    public function getData(): array
    {
        $result = array();
        foreach($this->getQueues() as $queue){
            $count = $this->getCount($queue);
            $failedCount = $this->jobs->countFailed($queue);
            [$status] = $this->getStatus($queue);
            $result[$queue] = [
                'count' => $count,
                'failed_count' => $failedCount,
                'status' => $status
            ];
        }
        return $result;
    }

    public function toggleStatus(): void
    {
        $queue = request()->input('queue');
        [$status, $statusKey] = $this->getStatus($queue);
        $queueStatusRunning = QueueStatus::RUNNING;
        $status = $status == $queueStatusRunning?
            QueueStatus::STOPPED : $queueStatusRunning;
        $this->setStatus($statusKey,$status);
    }

    public function setStatus(string $key,int $status): void
    {
        $this->jobs->setQueueStatus($key, $status);
    }

    public function getStatus(string $queue): array
    {
        $statusKey = QueueHelper::getStatusKey($queue);
        $status = $this->jobs->getQueueStatus($statusKey);
        return [$status , $statusKey];
    }

    protected function getCount(string $queue): int
    {
        return $this->jobs->count(QueueHelper::getQueueKey($queue));
    }

    private function getQueues(): array
    {
        return array_values(config('queue.names'));
    }
}
