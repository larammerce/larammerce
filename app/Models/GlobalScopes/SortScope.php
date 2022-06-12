<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/10/17
 * Time: 6:13 PM
 */

namespace App\Models\GlobalScopes;

use App\Utils\CMS\Setting\Sort\SortService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SortScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        $record = SortService::getRecord(get_class($model));
        if ($record){
            return $builder->orderBy($record->getField(), $record->getMethod());
        }
        return $builder;
    }
}
