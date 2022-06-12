<?php

namespace App\Utils\CMS\Setting\Excel;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ModelExport implements FromQuery, WithHeadings, WithEvents, WithMapping, ShouldAutoSize
{

    protected string $model_name;
    protected array $fields;
    protected array $model_relations;
    protected array $columns_division;
    protected bool $is_sample;

    public function __construct(string $model_name, array $fields = [], array $model_relations = [], bool $is_sample = false)
    {
        $this->model_name = $model_name;
        $this->fields = $fields;
        $this->model_relations = $model_relations;
        $this->columns_division = [];
        $this->exportable_relations = $this->model_name::getExportableRelations();
        $this->is_sample = $is_sample;
    }

    public function headings(): array
    {
        $headings = [];
        if (!$this->is_sample){
            $exportable_attributes = $this->model_name::getExportableAttributes();
            foreach ($exportable_attributes as $attribute){
                if (in_array($attribute,$this->fields)){
                    $headings[] = $attribute;
                }
            }
            $this->fields = $headings;                                              //sorted now
            $this->columns_division[$this->model_name] = count($headings);

            foreach ($this->exportable_relations as $model_name => $relation_method){
                if (in_array($relation_method,$this->model_relations)){
                    $extended_headings = $model_name::getExportableAttributes();
                    $headings = array_merge($headings,$extended_headings);
                    $this->columns_division[$model_name] = count($extended_headings);
                }
            }
        }
        else{
            $headings = $this->fields;
        }

        return $this->translateHeadings($headings);
    }

    public function map($row): array
    {
        $mapped_array = [];
        if (!$this->is_sample){
            foreach ($this->columns_division as $key => $value){
                if ($key == $this->model_name){
                    foreach ($this->fields as $field){
                        $mapped_array[] = $row[$field];
                    }
                }
                else{
                    $exportables = $key::getExportableAttributes();
                    for ($i=0;$i<$value;$i++){
                        $mapped_array[] = $row[$this->exportable_relations[$key]][$exportables[$i]] ?? '';
                    }
                }
            }
        }

        return $mapped_array;
    }

    #[ArrayShape([AfterSheet::class => "\Closure"])]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>
                function (AfterSheet $event) {
                    $event->sheet->setRightToLeft(true);
                },
        ];
    }

    private function translateHeadings($headings): array
    {
        $translated_headings = [];
        foreach($headings as $heading){
            $translated_headings[] = trans('structures.attributes.' . $heading);
        }
        return $translated_headings;
    }


    public function query(): Relation|Builder|\Illuminate\Database\Query\Builder
    {
        return $this->model_name::query()->with($this->model_relations);
    }
}
