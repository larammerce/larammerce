<?php

namespace App\Services\Product;

use App\Exceptions\PStructure\PStructureNotFoundException;
use App\Models\Product;
use App\Services\PStructure\PStructureService;
use Illuminate\Support\Collection;

class ProductService
{
    /**
     * @param int $p_structure_id
     * @return Collection|Product[]
     * @throws PStructureNotFoundException
     */
    public static function getAllProductsByPStructure(int $p_structure_id): Collection|array
    {
        if (!PStructureService::pStructureExists($p_structure_id))
            throw new PStructureNotFoundException("The exception with id `$p_structure_id` not found.");

        return Product::where("p_structure_id", $p_structure_id)->get();
    }
}
