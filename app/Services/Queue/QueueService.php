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
        if ($this->jobs->getQueueStatus(QueueHelper::getBufferKey($queue)) == QueueStatus::STOPPED)
            return;

        $jobs = $this->jobs->getJobs($queue);
        $this->jobs->pushJobs(QueueHelper::getBufferKey($queue),$jobs);
        $this->jobs->deleteJobs(QueueHelper::getQueueKey($queue));
    }

    public function resume(string $queue): void
    {
        if ($this->jobs->getQueueStatus(QueueHelper::getBufferKey($queue)) == QueueStatus::RUNNING)
            return;

        $jobs = $this->jobs->getJobsAndDeleteKey($queue);
        $this->jobs->pushJobs(QueueHelper::getQueueKey($queue),$jobs);
    }

    public function getAllQueuesData(): array
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
        $queue = request()->get('queue');
        [$status, $statusKey] = $this->getStatus($queue);
        $newStatus = $this->changeState($queue,$status);
        $this->jobs->setQueueStatus($statusKey, $newStatus);
    }

    private function getStatus(string $queue): array
    {
        $status_key = QueueHelper::getStatusKey($queue);
        $status = $this->jobs->getQueueStatus($status_key);
        return [$status , $status_key];
    }

    private function changeState(string $queue, int $status): int
    {
        if ($status == QueueStatus::RUNNING)
        {
            $this->pause($queue);
            $status = QueueStatus::STOPPED;
        }
        if ($status == QueueStatus::STOPPED)
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
            return $this->jobs->count($queue);
    }
}
