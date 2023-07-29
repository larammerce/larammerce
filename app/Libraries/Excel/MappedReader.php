<?php

namespace App\Libraries\Excel;

use App\Libraries\Excel\Concerns\ToArray;
use App\Libraries\Excel\Concerns\ToCollection;
use App\Libraries\Excel\Concerns\ToModel;
use App\Libraries\Excel\Concerns\WithCalculatedFormulas;
use App\Libraries\Excel\Concerns\WithFormatData;
use App\Libraries\Excel\Concerns\WithMappedCells;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MappedReader
{
    /**
     * @param  WithMappedCells  $import
     * @param  Worksheet  $worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function map(WithMappedCells $import, Worksheet $worksheet)
    {
        $mapped = [];
        foreach ($import->mapping() as $name => $coordinate) {
            $cell = Cell::make($worksheet, $coordinate);

            $mapped[$name] = $cell->getValue(
                null,
                $import instanceof WithCalculatedFormulas,
                $import instanceof WithFormatData
            );
        }

        if ($import instanceof ToModel) {
            $model = $import->model($mapped);

            if ($model) {
                $model->saveOrFail();
            }
        }

        if ($import instanceof ToCollection) {
            $import->collection(new Collection($mapped));
        }

        if ($import instanceof ToArray) {
            $import->array($mapped);
        }
    }
}
