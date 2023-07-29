<?php

namespace App\Libraries\Excel\Jobs;

use App\Libraries\Excel\Concerns\WithEvents;
use App\Libraries\Excel\Files\TemporaryFile;
use App\Libraries\Excel\Writer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CloseSheet implements ShouldQueue
{
    use Queueable, ProxyFailures;

    /**
     * @var object
     */
    private $sheetExport;

    /**
     * @var string
     */
    private $temporaryFile;

    /**
     * @var string
     */
    private $writerType;

    /**
     * @var int
     */
    private $sheetIndex;

    /**
     * @param  object  $sheetExport
     * @param  \App\Libraries\Excel\Files\TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @param  int  $sheetIndex
     */
    public function __construct($sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
    {
        $this->sheetExport   = $sheetExport;
        $this->temporaryFile = $temporaryFile;
        $this->writerType    = $writerType;
        $this->sheetIndex    = $sheetIndex;
    }

    /**
     * @param  \App\Libraries\Excel\Writer  $writer
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function handle(Writer $writer)
    {
        $writer = $writer->reopen(
            $this->temporaryFile,
            $this->writerType
        );

        $sheet = $writer->getSheetByIndex($this->sheetIndex);

        if ($this->sheetExport instanceof WithEvents) {
            $sheet->registerListeners($this->sheetExport->registerEvents());
        }

        $sheet->close($this->sheetExport);

        $writer->write(
            $this->sheetExport,
            $this->temporaryFile,
            $this->writerType
        );
    }
}
