<?php

namespace App\Utils\Excel\Factories;

use App\Utils\Excel\Concerns\MapsCsvSettings;
use App\Utils\Excel\Concerns\WithCustomCsvSettings;
use App\Utils\Excel\Concerns\WithLimit;
use App\Utils\Excel\Concerns\WithReadFilter;
use App\Utils\Excel\Concerns\WithStartRow;
use App\Utils\Excel\Exceptions\NoTypeDetectedException;
use App\Utils\Excel\Files\TemporaryFile;
use App\Utils\Excel\Filters\LimitFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class ReaderFactory
{
    use MapsCsvSettings;

    /**
     * @param  object  $import
     * @param  TemporaryFile  $file
     * @param  string  $readerType
     * @return IReader
     *
     * @throws Exception
     */
    public static function make($import, TemporaryFile $file, string $readerType = null): IReader
    {
        $reader = IOFactory::createReader(
            $readerType ?: static::identify($file)
        );

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(config('excel.imports.read_only', true));
        }

        if (method_exists($reader, 'setReadEmptyCells')) {
            $reader->setReadEmptyCells(!config('excel.imports.ignore_empty', false));
        }

        if ($reader instanceof Csv) {
            static::applyCsvSettings(config('excel.imports.csv', []));

            if ($import instanceof WithCustomCsvSettings) {
                static::applyCsvSettings($import->getCsvSettings());
            }

            $reader->setDelimiter(static::$delimiter);
            $reader->setEnclosure(static::$enclosure);
            $reader->setEscapeCharacter(static::$escapeCharacter);
            $reader->setContiguous(static::$contiguous);
            $reader->setInputEncoding(static::$inputEncoding);
        }

        if ($import instanceof WithReadFilter) {
            $reader->setReadFilter($import->readFilter());
        } elseif ($import instanceof WithLimit) {
            $reader->setReadFilter(new LimitFilter(
                $import instanceof WithStartRow ? $import->startRow() : 1,
                $import->limit()
            ));
        }

        return $reader;
    }

    /**
     * @param  TemporaryFile  $temporaryFile
     * @return string
     *
     * @throws NoTypeDetectedException
     */
    private static function identify(TemporaryFile $temporaryFile): string
    {
        try {
            return IOFactory::identify($temporaryFile->getLocalPath());
        } catch (Exception $e) {
            throw new NoTypeDetectedException(null, null, $e);
        }
    }
}
