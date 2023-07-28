<?php

namespace App\Features\Excel;


use App\Jobs\ExcelImportModelUpdate;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use function dispatch;
use function trans;

class ModelImport
{
    protected string $file;
    protected string $model_name;
    protected array $importable_attributes;             //data will be updated based on attribute indexed 0
    protected array $importable_attributes_validation;
    protected array $valid_rows;
    protected array $validation_errors;

    public function __construct(string $file, string $model_name)
    {
        $this->file = $file;
        $this->model_name = $model_name;
        $this->importable_attributes = array_keys($this->model_name::getImportableAttributes());
        $this->importable_attributes_validation = array_values($this->model_name::getImportableAttributes());
        $this->valid_rows = [];
        $this->validation_errors = [];
    }

    public function import(): void
    {
        $excel_file_path = $this->file;
        $spreadsheet = IOFactory::load($excel_file_path);
        $this->validateRows($spreadsheet);
        if (count($this->validation_errors)==0){
            $this->updateModel();
        }
    }

    public function validateRows($sheet): void
    {
        $active_sheet = $sheet->getActiveSheet();
        $active_sheet_rows = $active_sheet->toArray();
        $active_sheet_rows_count = count($active_sheet_rows);
        $importable_attributes_validation = $this->importable_attributes_validation;
        $headers_count = count($this->importable_attributes);
        $primary_model_attributes = $this->model_name::pluck($this->importable_attributes[0])->toArray();
        $valid = [];
        $invalid = [];
        for ($i=1;$i<$active_sheet_rows_count;$i++){
            $row = array_slice($active_sheet_rows[$i],0,$headers_count);
            if (in_array($row[0],$primary_model_attributes)){
                $validator = Validator::make($row,$importable_attributes_validation);
                if ($validator->fails()){
                    $message = trans('messages.excel.row_not_valid', ['row' => $i+1]);
                    $invalid[] = $message;
                }
                else{
                    $valid[] = $row;
                }
            }
            elseif ($row[0]!=null){
                $message = trans('messages.excel.row_not_valid', ['row' => $i+1]);
                $invalid[] = $message;
            }

        }
        $this->valid_rows = $valid;
        $this->validation_errors = $invalid;
    }

    private function updateModel(): void
    {
        $job = new ExcelImportModelUpdate($this->model_name,$this->valid_rows,$this->importable_attributes);
        dispatch($job);
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }
}
