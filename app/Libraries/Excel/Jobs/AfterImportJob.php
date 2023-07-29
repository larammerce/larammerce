<?php

namespace App\Libraries\Excel\Jobs;

use App\Libraries\Excel\Concerns\WithEvents;
use App\Libraries\Excel\Events\ImportFailed;
use App\Libraries\Excel\HasEventBus;
use App\Libraries\Excel\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class AfterImportJob implements ShouldQueue
{
    use Queueable, HasEventBus;

    /**
     * @var WithEvents
     */
    private $import;

    /**
     * @var \App\Libraries\Excel\Reader
     */
    private $reader;

    /**
     * @param  object  $import
     * @param  \App\Libraries\Excel\Reader  $reader
     */
    public function __construct($import, Reader $reader)
    {
        $this->import = $import;
        $this->reader = $reader;
    }

    public function handle()
    {
        if ($this->import instanceof ShouldQueue && $this->import instanceof WithEvents) {
            $this->reader->clearListeners();
            $this->reader->registerListeners($this->import->registerEvents());
        }

        $this->reader->afterImport($this->import);
    }

    /**
     * @param  Throwable  $e
     */
    public function failed(Throwable $e)
    {
        if ($this->import instanceof WithEvents) {
            $this->registerListeners($this->import->registerEvents());
            $this->raise(new ImportFailed($e));

            if (method_exists($this->import, 'failed')) {
                $this->import->failed($e);
            }
        }
    }
}
