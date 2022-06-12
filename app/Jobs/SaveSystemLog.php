<?php

namespace App\Jobs;

use App\Models\Interfaces\SystemLogContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaveSystemLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SystemLogContract $syslog;

    /**
     * Create a new job instance.
     * @param SystemLogContract $loggable
     * @return void
     */
    public function __construct(SystemLogContract $loggable)
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
