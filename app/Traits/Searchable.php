<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeClassicSearch(Builder $builder, array $term): Builder
    {
        foreach ($term as $key => $value) {
            if ($value !== null && in_array($key, static::$SEARCHABLE_FIELDS)) {
                $builder->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        return $builder;
    }
}
