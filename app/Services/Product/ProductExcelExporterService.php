<?php

namespace App\Services\Product;

use App\Models\PStructure;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductExcelExporterService implements WithHeadings, FromCollection, ShouldAutoSize, WithEvents {

    private PStructure $p_structure;
    private Collection $collection;
    private array $headings;

    public function __construct(PStructure $p_structure) {
        $this->p_structure = $p_structure;
        $result = ProductExporterService::exportDataArray($this->p_structure);
        $this->headings = $result["columns"];
        $this->collection = new Collection($result["rows"]);
    }

    public function headings(): array {
        return $this->headings;
    }

    public function collection(): Collection {
        return $this->collection;
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}