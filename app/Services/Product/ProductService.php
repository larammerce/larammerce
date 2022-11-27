<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\PStructure;
use Illuminate\Support\Collection;

class ProductService {
    /**
     * @param PStructure $p_structure
     * @return Collection|Product[]
     */
    public static function getAllProductsByPStructure(PStructure $p_structure): Collection|array {
        return $p_structure->products()->orderBy("id", "ASC")->get();
    }

    public static function chunkAllProductsByPStructure(PStructure $p_structure, int $count, callable $callback): bool {
        return $p_structure->products()->chunk($count, $callback);
    }
}
