<?php

namespace App\Libraries\Excel\Imports;

use App\Libraries\Excel\Concerns\WithColumnLimit;
use App\Libraries\Excel\Concerns\WithGroupedHeadingRow;
use App\Libraries\Excel\Concerns\WithHeadingRow;
use App\Libraries\Excel\Concerns\WithStartRow;
use App\Libraries\Excel\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HeadingRowExtractor
{
    /**
     * @const int
     */
    const DEFAULT_HEADING_ROW = 1;

    /**
     * @param  WithHeadingRow|mixed  $importable
     * @return int
     */
    public static function headingRow($importable): int
    {
        return method_exists($importable, 'headingRow')
            ? $importable->headingRow()
            : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param  \App\Libraries\Excel\Concerns\WithHeadingRow|mixed  $importable
     * @return int
     */
    public static function determineStartRow($importable): int
    {
        if ($importable instanceof WithStartRow) {
            return $importable->startRow();
        }

        // The start row is the row after the heading row if we have one!
        return $importable instanceof WithHeadingRow
            ? self::headingRow($importable) + 1
            : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param  Worksheet  $worksheet
     * @param  \App\Libraries\Excel\Concerns\WithHeadingRow|mixed  $importable
     * @return array
     */
    public static function extract(Worksheet $worksheet, $importable): array
    {
        if (!$importable instanceof WithHeadingRow) {
            return [];
        }

        $headingRowNumber = self::headingRow($importable);
        $rows             = iterator_to_array($worksheet->getRowIterator($headingRowNumber, $headingRowNumber));
        $headingRow       = head($rows);
        $endColumn        = $importable instanceof WithColumnLimit ? $importable->endColumn() : null;

        return HeadingRowFormatter::format((new Row($headingRow))->toArray(null, false, false, $endColumn));
    }

    /**
     * @param  array  $headingRow
     * @param  WithGroupedHeadingRow|mixed  $importable
     * @return array
     */
    public static function extractGrouping($headingRow, $importable)
    {
        $headerIsGrouped = array_fill(0, count($headingRow), false);

        if (!$importable instanceof WithGroupedHeadingRow) {
            return $headerIsGrouped;
        }

        array_walk($headerIsGrouped, function (&$value, $key) use ($headingRow) {
            if (array_count_values($headingRow)[$headingRow[$key]] > 1) {
                $value = true;
            }
        });

        return $headerIsGrouped;
    }
}
