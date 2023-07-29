<?php

namespace App\Libraries\Excel\Imports;

use App\Libraries\Excel\Concerns\SkipsEmptyRows;
use App\Libraries\Excel\Concerns\ToModel;
use App\Libraries\Excel\Concerns\WithBatchInserts;
use App\Libraries\Excel\Concerns\WithCalculatedFormulas;
use App\Libraries\Excel\Concerns\WithColumnLimit;
use App\Libraries\Excel\Concerns\WithFormatData;
use App\Libraries\Excel\Concerns\WithMapping;
use App\Libraries\Excel\Concerns\WithProgressBar;
use App\Libraries\Excel\Concerns\WithValidation;
use App\Libraries\Excel\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ModelImporter
{
    /**
     * @var ModelManager
     */
    private $manager;

    /**
     * @param  ModelManager  $manager
     */
    public function __construct(ModelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  Worksheet  $worksheet
     * @param  ToModel  $import
     * @param  int|null  $startRow
     * @param  string|null  $endColumn
     *
     * @throws \App\Libraries\Excel\Validators\ValidationException
     */
    public function import(Worksheet $worksheet, ToModel $import, int $startRow = 1)
    {
        if ($startRow > $worksheet->getHighestRow()) {
            return;
        }

        $headingRow       = HeadingRowExtractor::extract($worksheet, $import);
        $headerIsGrouped  = HeadingRowExtractor::extractGrouping($headingRow, $import);
        $batchSize        = $import instanceof WithBatchInserts ? $import->batchSize() : 1;
        $endRow           = EndRowFinder::find($import, $startRow, $worksheet->getHighestRow());
        $progessBar       = $import instanceof WithProgressBar;
        $withMapping      = $import instanceof WithMapping;
        $withCalcFormulas = $import instanceof WithCalculatedFormulas;
        $formatData       = $import instanceof WithFormatData;
        $withValidation   = $import instanceof WithValidation && method_exists($import, 'prepareForValidation');
        $endColumn        = $import instanceof WithColumnLimit ? $import->endColumn() : null;

        $this->manager->setRemembersRowNumber(method_exists($import, 'rememberRowNumber'));

        $i = 0;
        foreach ($worksheet->getRowIterator($startRow, $endRow) as $spreadSheetRow) {
            $i++;

            $row = new Row($spreadSheetRow, $headingRow, $headerIsGrouped);
            if (!$import instanceof SkipsEmptyRows || ($import instanceof SkipsEmptyRows && !$row->isEmpty($withCalcFormulas))) {
                $rowArray = $row->toArray(null, $withCalcFormulas, $formatData, $endColumn);

                if ($withValidation) {
                    $rowArray = $import->prepareForValidation($rowArray, $row->getIndex());
                }

                if ($withMapping) {
                    $rowArray = $import->map($rowArray);
                }

                $this->manager->add(
                    $row->getIndex(),
                    $rowArray
                );

                // Flush each batch.
                if (($i % $batchSize) === 0) {
                    $this->manager->flush($import, $batchSize > 1);
                    $i = 0;

                    if ($progessBar) {
                        $import->getConsoleOutput()->progressAdvance($batchSize);
                    }
                }
            }
        }

        // Flush left-overs.
        $this->manager->flush($import, $batchSize > 1);
    }
}
