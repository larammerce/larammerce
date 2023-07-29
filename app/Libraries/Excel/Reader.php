<?php

namespace App\Libraries\Excel;

use App\Libraries\Excel\Concerns\HasReferencesToOtherSheets;
use App\Libraries\Excel\Concerns\SkipsUnknownSheets;
use App\Libraries\Excel\Concerns\WithCalculatedFormulas;
use App\Libraries\Excel\Concerns\WithChunkReading;
use App\Libraries\Excel\Concerns\WithCustomValueBinder;
use App\Libraries\Excel\Concerns\WithEvents;
use App\Libraries\Excel\Concerns\WithFormatData;
use App\Libraries\Excel\Concerns\WithMultipleSheets;
use App\Libraries\Excel\Events\AfterImport;
use App\Libraries\Excel\Events\BeforeImport;
use App\Libraries\Excel\Events\ImportFailed;
use App\Libraries\Excel\Exceptions\NoTypeDetectedException;
use App\Libraries\Excel\Exceptions\SheetNotFoundException;
use App\Libraries\Excel\Factories\ReaderFactory;
use App\Libraries\Excel\Files\TemporaryFile;
use App\Libraries\Excel\Files\TemporaryFileFactory;
use App\Libraries\Excel\Transactions\TransactionHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

/** @mixin Spreadsheet */
class Reader
{
    use DelegatedMacroable, HasEventBus;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var object[]
     */
    protected $sheetImports = [];

    /**
     * @var \App\Libraries\Excel\Files\TemporaryFile
     */
    protected $currentFile;

    /**
     * @var \App\Libraries\Excel\Files\TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @var TransactionHandler
     */
    protected $transaction;

    /**
     * @var IReader
     */
    protected $reader;

    /**
     * @param  TemporaryFileFactory  $temporaryFileFactory
     * @param  TransactionHandler  $transaction
     */
    public function __construct(TemporaryFileFactory $temporaryFileFactory, TransactionHandler $transaction)
    {
        $this->setDefaultValueBinder();

        $this->transaction          = $transaction;
        $this->temporaryFileFactory = $temporaryFileFactory;
    }

    public function __sleep()
    {
        return ['spreadsheet', 'sheetImports', 'currentFile', 'temporaryFileFactory', 'reader'];
    }

    public function __wakeup()
    {
        $this->transaction = app(TransactionHandler::class);
    }

    /**
     * @param  object  $import
     * @param  string|UploadedFile  $filePath
     * @param  string|null  $readerType
     * @param  string|null  $disk
     * @return \Illuminate\Foundation\Bus\PendingDispatch|$this
     *
     * @throws \App\Libraries\Excel\Exceptions\NoTypeDetectedException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws Exception
     */
    public function read($import, $filePath, string $readerType = null, string $disk = null)
    {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);

        if ($import instanceof WithChunkReading) {
            return (new ChunkReader)->read($import, $this, $this->currentFile);
        }

        try {
            $this->loadSpreadsheet($import, $this->reader);

            ($this->transaction)(function () use ($import) {
                $sheetsToDisconnect = [];

                foreach ($this->sheetImports as $index => $sheetImport) {
                    if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                        $sheet->import($sheetImport, $sheet->getStartRow($sheetImport));

                        // when using WithCalculatedFormulas we need to keep the sheet until all sheets are imported
                        if (!($sheetImport instanceof HasReferencesToOtherSheets)) {
                            $sheet->disconnect();
                        } else {
                            $sheetsToDisconnect[] = $sheet;
                        }
                    }
                }

                foreach ($sheetsToDisconnect as $sheet) {
                    $sheet->disconnect();
                }
            });

            $this->afterImport($import);
        } catch (Throwable $e) {
            $this->raise(new ImportFailed($e));
            $this->garbageCollect();
            throw $e;
        }

        return $this;
    }

    /**
     * @param  object  $import
     * @param  string|UploadedFile  $filePath
     * @param  string  $readerType
     * @param  string|null  $disk
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \App\Libraries\Excel\Exceptions\NoTypeDetectedException
     * @throws \App\Libraries\Excel\Exceptions\SheetNotFoundException
     */
    public function toArray($import, $filePath, string $readerType = null, string $disk = null): array
    {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);

        $this->loadSpreadsheet($import);

        $sheets             = [];
        $sheetsToDisconnect = [];
        foreach ($this->sheetImports as $index => $sheetImport) {
            $calculatesFormulas = $sheetImport instanceof WithCalculatedFormulas;
            $formatData         = $sheetImport instanceof WithFormatData;
            if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                $sheets[$index] = $sheet->toArray($sheetImport, $sheet->getStartRow($sheetImport), null, $calculatesFormulas, $formatData);

                // when using WithCalculatedFormulas we need to keep the sheet until all sheets are imported
                if (!($sheetImport instanceof HasReferencesToOtherSheets)) {
                    $sheet->disconnect();
                } else {
                    $sheetsToDisconnect[] = $sheet;
                }
            }
        }

        foreach ($sheetsToDisconnect as $sheet) {
            $sheet->disconnect();
        }

        $this->afterImport($import);

        return $sheets;
    }

    /**
     * @param  object  $import
     * @param  string|UploadedFile  $filePath
     * @param  string  $readerType
     * @param  string|null  $disk
     * @return Collection
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \App\Libraries\Excel\Exceptions\NoTypeDetectedException
     * @throws \App\Libraries\Excel\Exceptions\SheetNotFoundException
     */
    public function toCollection($import, $filePath, string $readerType = null, string $disk = null): Collection
    {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);
        $this->loadSpreadsheet($import);

        $sheets             = new Collection();
        $sheetsToDisconnect = [];
        foreach ($this->sheetImports as $index => $sheetImport) {
            $calculatesFormulas = $sheetImport instanceof WithCalculatedFormulas;
            $formatData         = $sheetImport instanceof WithFormatData;
            if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                $sheets->put($index, $sheet->toCollection($sheetImport, $sheet->getStartRow($sheetImport), null, $calculatesFormulas, $formatData));

                // when using WithCalculatedFormulas we need to keep the sheet until all sheets are imported
                if (!($sheetImport instanceof HasReferencesToOtherSheets)) {
                    $sheet->disconnect();
                } else {
                    $sheetsToDisconnect[] = $sheet;
                }
            }
        }

        foreach ($sheetsToDisconnect as $sheet) {
            $sheet->disconnect();
        }

        $this->afterImport($import);

        return $sheets;
    }

    /**
     * @return Spreadsheet
     */
    public function getDelegate()
    {
        return $this->spreadsheet;
    }

    /**
     * @return $this
     */
    public function setDefaultValueBinder(): self
    {
        Cell::setValueBinder(
            app(config('excel.value_binder.default', DefaultValueBinder::class))
        );

        return $this;
    }

    /**
     * @param  object  $import
     */
    public function loadSpreadsheet($import)
    {
        $this->sheetImports = $this->buildSheetImports($import);

        $this->readSpreadsheet();

        // When no multiple sheets, use the main import object
        // for each loaded sheet in the spreadsheet
        if (!$import instanceof WithMultipleSheets) {
            $this->sheetImports = array_fill(0, $this->spreadsheet->getSheetCount(), $import);
        }

        $this->beforeImport($import);
    }

    public function readSpreadsheet()
    {
        $this->spreadsheet = $this->reader->load(
            $this->currentFile->getLocalPath()
        );
    }

    /**
     * @param  object  $import
     */
    public function beforeImport($import)
    {
        $this->raise(new BeforeImport($this, $import));
    }

    /**
     * @param  object  $import
     */
    public function afterImport($import)
    {
        $this->raise(new AfterImport($this, $import));

        $this->garbageCollect();
    }

    /**
     * @return IReader
     */
    public function getPhpSpreadsheetReader(): IReader
    {
        return $this->reader;
    }

    /**
     * @param  object  $import
     * @return array
     */
    public function getWorksheets($import): array
    {
        // Csv doesn't have worksheets.
        if (!method_exists($this->reader, 'listWorksheetNames')) {
            return ['Worksheet' => $import];
        }

        $worksheets     = [];
        $worksheetNames = $this->reader->listWorksheetNames($this->currentFile->getLocalPath());
        if ($import instanceof WithMultipleSheets) {
            $sheetImports = $import->sheets();

            foreach ($sheetImports as $index => $sheetImport) {
                // Translate index to name.
                if (is_numeric($index)) {
                    $index = $worksheetNames[$index] ?? $index;
                }

                // Specify with worksheet name should have which import.
                $worksheets[$index] = $sheetImport;
            }

            // Load specific sheets.
            if (method_exists($this->reader, 'setLoadSheetsOnly')) {
                $this->reader->setLoadSheetsOnly(
                    collect($worksheetNames)->intersect(array_keys($worksheets))->values()->all()
                );
            }
        } else {
            // Each worksheet the same import class.
            foreach ($worksheetNames as $name) {
                $worksheets[$name] = $import;
            }
        }

        return $worksheets;
    }

    /**
     * @return array
     */
    public function getTotalRows(): array
    {
        $info = $this->reader->listWorksheetInfo($this->currentFile->getLocalPath());

        $totalRows = [];
        foreach ($info as $sheet) {
            $totalRows[$sheet['worksheetName']] = $sheet['totalRows'];
        }

        return $totalRows;
    }

    /**
     * @param $import
     * @param $sheetImport
     * @param $index
     * @return Sheet|null
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws SheetNotFoundException
     */
    protected function getSheet($import, $sheetImport, $index)
    {
        try {
            return Sheet::make($this->spreadsheet, $index);
        } catch (SheetNotFoundException $e) {
            if ($import instanceof SkipsUnknownSheets) {
                $import->onUnknownSheet($index);

                return null;
            }

            if ($sheetImport instanceof SkipsUnknownSheets) {
                $sheetImport->onUnknownSheet($index);

                return null;
            }

            throw $e;
        }
    }

    /**
     * @param  object  $import
     * @return array
     */
    private function buildSheetImports($import): array
    {
        $sheetImports = [];
        if ($import instanceof WithMultipleSheets) {
            $sheetImports = $import->sheets();

            // When only sheet names are given and the reader has
            // an option to load only the selected sheets.
            if (
                method_exists($this->reader, 'setLoadSheetsOnly')
                && count(array_filter(array_keys($sheetImports), 'is_numeric')) === 0
            ) {
                $this->reader->setLoadSheetsOnly(array_keys($sheetImports));
            }
        }

        return $sheetImports;
    }

    /**
     * @param  object  $import
     * @param  string|UploadedFile  $filePath
     * @param  string|null  $readerType
     * @param  string  $disk
     * @return IReader
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \App\Libraries\Excel\Exceptions\NoTypeDetectedException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws InvalidArgumentException
     */
    private function getReader($import, $filePath, string $readerType = null, string $disk = null): IReader
    {
        $shouldQueue = $import instanceof ShouldQueue;
        if ($shouldQueue && !$import instanceof WithChunkReading) {
            throw new InvalidArgumentException('ShouldQueue is only supported in combination with WithChunkReading.');
        }

        if ($import instanceof WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        if ($import instanceof WithCustomValueBinder) {
            Cell::setValueBinder($import);
        }

        $fileExtension     = pathinfo($filePath, PATHINFO_EXTENSION);
        $temporaryFile     = $shouldQueue ? $this->temporaryFileFactory->make($fileExtension) : $this->temporaryFileFactory->makeLocal(null, $fileExtension);
        $this->currentFile = $temporaryFile->copyFrom(
            $filePath,
            $disk
        );

        return ReaderFactory::make(
            $import,
            $this->currentFile,
            $readerType
        );
    }

    /**
     * Garbage collect.
     */
    private function garbageCollect()
    {
        $this->clearListeners();
        $this->setDefaultValueBinder();

        // Force garbage collecting
        unset($this->sheetImports, $this->spreadsheet);

        $this->currentFile->delete();
    }
}
