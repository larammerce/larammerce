<?php

namespace App\Repositories;

use App\Enums\Queue\QueueStatus;
use App\Interfaces\Repositories\JobRepository;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\DB;

class JobRepositoryImpl implements JobRepository
{
    private string $prefix;

    /**
     * @param RedisFactory $redis
     */
    public function __construct(
        public RedisFactory $redis,
    )
    {
        $this->prefix = 'queues:';
    }

    /**
     * @param string $queue
     * @return int
     */
    public function countFailed(string $queue): int
    {
        return DB::table('failed_jobs')
            ->where('queue' , $queue)
            ->get()->count();
    }

    /**
     * @param string $queue
     * @return int
     */
    public function count(string $queue): int
    {
        return (int) $this->connection()
            ->llen($this->prefix . $queue);
    }

    /**
     * @param string $queue
     * @return array
     */
    public function getJobs(string $queue): array
    {
        return $this->connection()
            ->lrange($this->prefix . $queue , 0 , -1);
    }

    protected function connection(): Connection
    {
        return $this->redis->connection();
    }

    /**
     * @param string $prefix
     * @return void
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string $key
     * @param array $data
     * @return void
     */
    public function pushJobs(string $key, array $data): void
    {
        $this->connection()->lpush($key, $data);
    }

    /**
     * @param string $key
     * @return void
     */
    public function deleteKey(string $key): void
    {
        $this->connection()->unlink($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function checkIfKeyExists(string $key): bool
    {
        return $this->connection()->exists($key);
    }

    /**
     * @param string $key
     * @return void
     */
    public function deleteJobs(string $key): void
    {
        $this->connection()->ltrim($key, 0 , -1);
    }

    /**
     * @param string $key
     * @return array
     */
    public function getJobsAndDeleteKey(string $key): array
    {
        $jobs = $this->getJobs($key);
        $this->deleteKey($key);
        return $jobs;
    }

    /**
     * @param string $key
     * @return int
     */
    public function getQueueStatus(string $key): int
    {
        if(!$this->checkIfKeyExists($key))
            $this->connection()->append($key, QueueStatus::RUNNING);

        return (int) $this->connection()->get($key);
    }

    /**
     * @param string $key
     * @param int $status
     * @return void
     */
    public function setQueueStatus(string $key, int $status): void
    {
        $this->connection()->del($key);
        $this->connection()->append($key, $status);
    }
}
