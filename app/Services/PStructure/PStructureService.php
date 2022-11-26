<?php

namespace App\Services\PStructure;

use App\Models\PStructure;

class PStructureService
{
    public static function pStructureExists(int $p_structure_id): bool
    {
        return PStructure::where("id", $p_structure_id)->count() > 0;
    }
}
