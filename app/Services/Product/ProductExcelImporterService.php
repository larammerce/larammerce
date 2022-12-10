<?php

namespace App\Services\Product;

use App\Jobs\Product\ProductImportFromDataArray;
use App\Models\PStructure;
use App\Validations\ProductValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ProductExcelImporterService implements ToModel, WithHeadingRow {

    private PStructure $p_structure;

    public function __construct(PStructure $p_structure) {
        HeadingRowFormatter::default('none');
        $this->p_structure = $p_structure;
    }

    /**
     * @throws ValidationException
     */
    public function model(array $row): void {
        $validator = Validator::make($row, ProductValidation::EXCEL_ROW);
        $validator->validate();
        dispatch(new ProductImportFromDataArray($this->p_structure, $row));
    }

    public function headingRow(): int {
        return 1;
    }
}
