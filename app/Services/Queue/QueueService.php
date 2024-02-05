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

    public function pause(string $queue): void
    {
        $this->swapData(
            $queue,
            QueueHelper::getBufferKey($queue),
            QueueHelper::getQueueKey($queue),
        );
    }

    public function resume(string $queue): void
    {
        $this->swapData(
            $queue,
            QueueHelper::getQueueKey($queue),
            QueueHelper::getBufferKey($queue),
        );
    }

    public function swapData($queue, $pushKey, $deleteKey): void
    {
        $jobs = $this->jobs->getJobs(QueueHelper::getQueueKey($queue));
        $this->jobs->pushJobs($pushKey, $jobs);
        $this->jobs->deleteKey($deleteKey);
    }

    public function getAllData(): array
    {
        $result = array();
        foreach(config('queue.queues') as $queue){
            $processingCount = $this->getQueueCount($queue);
            $failedCount = $this->jobs->countFailed($queue);
            [$status] = $this->getStatus($queue);
            $result[$queue] = [
                'count' => $processingCount,
                'failed_count' => $failedCount,
                'status' => $status
            ];
        }
        return $result;
    }

    public function toggleState(): void
    {
        $queue = request()->input('queue');
        [$status, $statusKey] = $this->getStatus($queue);
        $newStatus = $this->changeState($queue,$status);
        $this->jobs->setQueueStatus($statusKey, $newStatus);
    }

    private function getStatus(string $queue): array
    {
        $statusKey = QueueHelper::getStatusKey($queue);
        $status = $this->jobs->getQueueStatus($statusKey);
        return [$status , $statusKey];
    }

    private function changeState(string $queue, int $status): int
    {
        if ($status == QueueStatus::RUNNING)
        {
            $this->pause($queue);
            $status = QueueStatus::STOPPED;
        }
        else if ($status == QueueStatus::STOPPED)
        {
            $this->resume($queue);
            $status = QueueStatus::RUNNING;
        }
        return $status;
    }

    protected function getQueueCount(string $queue)
    {
        if($this->getStatus($queue) == QueueStatus::STOPPED)
            return $this->jobs->count(QueueHelper::getBufferKey($queue));
        else
            return $this->jobs->count(QueueHelper::getQueueKey($queue));
    }
}
