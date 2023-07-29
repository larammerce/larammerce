<?php

namespace App\Libraries\Excel\Concerns;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface WithStyles
{
    public function styles(Worksheet $sheet);
}
