<?php

namespace App\Jobs;

use App\Interfaces\SystemLogInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaveSystemLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SystemLogInterface $syslog;

    /**
     * Create a new job instance.
     * @param SystemLogInterface $loggable
     * @return void
     */
    public function __construct(SystemLogInterface $loggable)
    {
        $this->syslog = $loggable;
        $this->queue = config('queue.names.admin') . "_SYS_LOG";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->syslog->translate();
        $saved = $this->syslog->save();
        if (!$saved) {
            Log::error("saving system log failed: " . json_encode($this->syslog));
        }
    }
}
