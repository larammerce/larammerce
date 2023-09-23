<?php

namespace App\Utils\CMS\Setting\Excel;


use App\Utils\Jalali\JDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use App\Utils\Excel\Concerns\FromQuery;
use App\Utils\Excel\Concerns\ShouldAutoSize;
use App\Utils\Excel\Concerns\WithEvents;
use App\Utils\Excel\Concerns\WithHeadings;
use App\Utils\Excel\Concerns\WithMapping;
use App\Utils\Excel\Events\AfterSheet;

class ModelExport implements FromQuery, WithHeadings, WithEvents, WithMapping, ShouldAutoSize
{

    protected string $model_name;
    protected array $fields;
    protected array $model_relations;
    protected array $columns_division;
    protected bool $is_sample;
    protected array $query_data;
    protected string $raw_query;
    protected string $raw_select;
    protected array $group_by;
    protected array $extended_attributes;
    protected array $exportable_relations;

    public function __construct(
        string $model_name,
        array  $fields = [],
        array  $model_relations = [],
        bool   $is_sample = false,
        array  $query_data = [],
        array  $group_by = [],
        array  $extended_attributes = [],
        string $raw_query = "",
        string $raw_select = ""
    ) {
        $this->model_name = $model_name;
        $this->fields = $fields;
        $this->model_relations = $model_relations;
        $this->columns_division = [];
        $this->exportable_relations = $this->model_name::getExportableRelations();
        $this->is_sample = $is_sample;
        $this->query_data = $query_data;
        $this->raw_query = $raw_query;
        $this->group_by = $group_by;
        $this->raw_select = $raw_select;
        $this->extended_attributes = $extended_attributes;
    }

    public function headings(): array {
        $headings = [];
        if (!$this->is_sample) {
            $exportable_attributes = $this->model_name::getExportableAttributes($this->extended_attributes);
            foreach ($exportable_attributes as $attribute) {
                if (in_array($attribute, $this->fields)) {
                    $headings[] = $attribute;
                }
            }
            $this->fields = $headings;                                              //sorted now
            $this->columns_division[$this->model_name] = $headings;

            foreach ($this->exportable_relations as $model_name => $relation_method) {
                if (in_array($relation_method["name"], $this->model_relations)) {
                    $extended_headings = $relation_method["fields"] ?? $model_name::getExportableAttributes();
                    $headings = array_merge($headings, $extended_headings);
                    $this->columns_division[$model_name] = $extended_headings;
                }
            }
        } else {
            $headings = $this->fields;
        }

        return $this->translateHeadings($headings);
    }

    public function map($row): array {
        $mapped_array = [];
        if (!$this->is_sample) {
            foreach ($this->columns_division as $key => $headings) {
                if ($key == $this->model_name) {
                    foreach ($this->fields as $field) {
                        $value = $row[$field];
                        if (str_ends_with($field, "_at")) {
                            $value = JDate::forge($value)->format("Y/m/d H:i");
                        }
                        $mapped_array[] = $value;
                    }
                } else {
                    foreach ($headings as $heading) {
                        $value = $row[$this->exportable_relations[$key]["name"]][$heading] ?? '';;
                        if (str_ends_with($heading, "_at")) {
                            $value = JDate::forge($value)->format("Y/m/d H:i");
                        }
                        $mapped_array[] = $value;
                    }
                }
            }
        }

        return $mapped_array;
    }

    #[ArrayShape([AfterSheet::class => "\Closure"])]
    public function registerEvents(): array {
        return [
            AfterSheet::class =>
                function (AfterSheet $event) {
                    $event->sheet->setRightToLeft(true);
                },
        ];
    }

    private function translateHeadings($headings): array {
        $translated_headings = [];
        foreach ($headings as $heading) {
            $translated_headings[] = trans('structures.attributes.' . $heading);
        }
        return $translated_headings;
    }


    public function query(): Relation|Builder|\Illuminate\Database\Query\Builder {
        $result = $this->model_name::query();
        foreach ($this->query_data as $query_key => $query_value) {
            $result = $result->where($query_key, $query_value);
        }

        if (strlen($this->raw_query) > 0) {
            $result = $result->whereRaw(DB::raw($this->raw_query));
        }

        if (count($this->group_by) > 0) {
            $result = $result->groupBy($this->group_by);
        }

        if (strlen($this->raw_select) > 0) {
            $result = $result->selectRaw(DB::raw($this->raw_select));
        }

        return $result->with($this->model_relations);
    }
}
