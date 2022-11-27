<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\PStructure;

class ProductImporterService {
    public static function importFromDataArray(PStructure $p_structure, array $data_array): Product {
        dd($p_structure, $data_array);
    }
}