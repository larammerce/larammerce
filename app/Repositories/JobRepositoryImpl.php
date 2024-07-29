<?php

namespace App\Repositories;

use App\Enums\Queue\QueueStatus;
use App\Interfaces\Repositories\JobRepository;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\DB;

class JobRepositoryImpl implements JobRepository
{
    /**
     * @param RedisFactory $redis
     */
    public function __construct(
        public RedisFactory $redis,
    )
    {
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
            ->llen( $queue);
    }
    protected function connection(): Connection
    {
        return $this->redis->connection();
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function checkIfKeyExists(string $key): bool
    {
        return $this->connection()->exists($key);
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
