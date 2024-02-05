<?php

namespace App\Jobs\Directory;

use App\Enums\Queue\QueueDispatchType;
use App\Enums\Queue\QueuePriority;
use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Exceptions\Product\ProductNotFoundException;
use App\Jobs\Job;
use App\Models\Directory;
use App\Models\PStructure;
use App\Services\Directory\DirectoryService;
use App\Services\Product\ProductImporterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncDirectories extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    private Directory $directory;

    public function __construct(Directory $directory) {
        $this->directory = $directory;
        $this->queue = config('queue.names.admin_automatic_default');
    }

    public function handle() {
        DirectoryService::syncWithUpstream($this->directory);
    }

    /**
     * @return int|null
     */
    public function getDispatchType(): ?int
    {
        return QueueDispatchType::AUTOMATIC;
    }

    /**
     * @return int|null
     */
    public function getQueuePriority(): ?int
    {
        return QueuePriority::DEFAULT;
    }
}
