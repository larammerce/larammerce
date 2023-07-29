<?php

namespace App\Libraries\Excel;

use App\Libraries\Excel\Concerns\ShouldQueueWithoutChain;
use App\Libraries\Excel\Concerns\WithChunkReading;
use App\Libraries\Excel\Concerns\WithEvents;
use App\Libraries\Excel\Concerns\WithLimit;
use App\Libraries\Excel\Concerns\WithProgressBar;
use App\Libraries\Excel\Events\BeforeImport;
use App\Libraries\Excel\Files\TemporaryFile;
use App\Libraries\Excel\Imports\HeadingRowExtractor;
use App\Libraries\Excel\Jobs\AfterImportJob;
use App\Libraries\Excel\Jobs\QueueImport;
use App\Libraries\Excel\Jobs\ReadChunk;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Collection;
use Throwable;

class ChunkReader
{
    /**
     * @param  WithChunkReading  $import
     * @param  Reader  $reader
     * @param  TemporaryFile  $temporaryFile
     * @return \Illuminate\Foundation\Bus\PendingDispatch|null
     */
    public function read(WithChunkReading $import, Reader $reader, TemporaryFile $temporaryFile)
    {
        if ($import instanceof WithEvents && isset($import->registerEvents()[BeforeImport::class])) {
            $reader->beforeImport($import);
        }

        $chunkSize    = $import->chunkSize();
        $totalRows    = $reader->getTotalRows();
        $worksheets   = $reader->getWorksheets($import);
        $queue        = property_exists($import, 'queue') ? $import->queue : null;
        $delayCleanup = property_exists($import, 'delayCleanup') ? $import->delayCleanup : 600;

        if ($import instanceof WithProgressBar) {
            $import->getConsoleOutput()->progressStart(array_sum($totalRows));
        }

        $jobs = new Collection();
        foreach ($worksheets as $name => $sheetImport) {
            $startRow = HeadingRowExtractor::determineStartRow($sheetImport);

            if ($sheetImport instanceof WithLimit) {
                $limit = $sheetImport->limit();

                if ($limit <= $totalRows[$name]) {
                    $totalRows[$name] = $sheetImport->limit();
                }
            }

            for ($currentRow = $startRow; $currentRow <= $totalRows[$name]; $currentRow += $chunkSize) {
                $jobs->push(new ReadChunk(
                    $import,
                    $reader->getPhpSpreadsheetReader(),
                    $temporaryFile,
                    $name,
                    $sheetImport,
                    $currentRow,
                    $chunkSize
                ));
            }
        }

        $afterImportJob = new AfterImportJob($import, $reader);

        if ($import instanceof ShouldQueueWithoutChain) {
            $jobs->push($afterImportJob->delay($delayCleanup));

            return $jobs->each(function ($job) use ($queue) {
                dispatch($job->onQueue($queue));
            });
        }

        $jobs->push($afterImportJob);

        if ($import instanceof ShouldQueue) {
            return new PendingDispatch(
                (new QueueImport($import))->chain($jobs->toArray())
            );
        }

        $jobs->each(function ($job) {
            try {
                dispatch_now($job);
            } catch (Throwable $e) {
                if (method_exists($job, 'failed')) {
                    $job->failed($e);
                }
                throw $e;
            }
        });

        if ($import instanceof WithProgressBar) {
            $import->getConsoleOutput()->progressFinish();
        }

        unset($jobs);

        return null;
    }
}
