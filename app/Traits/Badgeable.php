<?php


namespace App\Traits;


use App\Models\Badge;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


trait Badgeable
{

    public function badges(): MorphToMany
    {
        return $this->morphToMany(Badge::class, 'badgeable');
    }

}
