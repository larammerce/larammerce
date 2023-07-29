<?php

namespace App\Services\Product;

use App\Libraries\Excel\Concerns\FromCollection;
use App\Libraries\Excel\Concerns\ShouldAutoSize;
use App\Libraries\Excel\Concerns\WithEvents;
use App\Libraries\Excel\Concerns\WithHeadings;
use App\Libraries\Excel\Events\AfterSheet;
use App\Models\PStructure;
use Illuminate\Support\Collection;

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
