<?php

namespace App\Libraries\Excel\Concerns;

use App\Libraries\Excel\Events\AfterImport;
use App\Libraries\Excel\Events\AfterSheet;
use App\Libraries\Excel\Events\BeforeExport;
use App\Libraries\Excel\Events\BeforeImport;
use App\Libraries\Excel\Events\BeforeSheet;
use App\Libraries\Excel\Events\BeforeWriting;
use App\Libraries\Excel\Events\ImportFailed;

trait RegistersEventListeners
{
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $listeners = [];

        if (method_exists($this, 'beforeExport')) {
            $listeners[BeforeExport::class] = [static::class, 'beforeExport'];
        }

        if (method_exists($this, 'beforeWriting')) {
            $listeners[BeforeWriting::class] = [static::class, 'beforeWriting'];
        }

        if (method_exists($this, 'beforeImport')) {
            $listeners[BeforeImport::class] = [static::class, 'beforeImport'];
        }

        if (method_exists($this, 'afterImport')) {
            $listeners[AfterImport::class] = [static::class, 'afterImport'];
        }

        if (method_exists($this, 'importFailed')) {
            $listeners[ImportFailed::class] = [static::class, 'importFailed'];
        }

        if (method_exists($this, 'beforeSheet')) {
            $listeners[BeforeSheet::class] = [static::class, 'beforeSheet'];
        }

        if (method_exists($this, 'afterSheet')) {
            $listeners[AfterSheet::class] = [static::class, 'afterSheet'];
        }

        return $listeners;
    }
}
