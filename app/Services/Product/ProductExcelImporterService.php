<?php

namespace App\Services\Product;

use App\Jobs\Product\ProductImportFromDataArray;
use App\Libraries\Excel\Concerns\ToModel;
use App\Libraries\Excel\Concerns\WithHeadingRow;
use App\Libraries\Excel\Concerns\WithValidation;
use App\Libraries\Excel\Imports\HeadingRowFormatter;
use App\Models\PStructure;
use App\Validations\ProductValidation;
use Illuminate\Validation\ValidationException;

class ProductExcelImporterService implements ToModel, WithHeadingRow, WithValidation {

    private PStructure $p_structure;

    public function __construct(PStructure $p_structure) {
        HeadingRowFormatter::default('none');
        $this->p_structure = $p_structure;
    }

    /**
     * @throws ValidationException
     */
    public function model(array $row): void {
        dispatch(new ProductImportFromDataArray($this->p_structure, $row));
    }

    public function headingRow(): int {
        return 1;
    }

    public function rules(): array {
        return ProductValidation::EXCEL_ROW;
    }
}
