<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 12/13/18
 * Time: 2:14 PM
 */

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FullTextSearch
{

    /**
     * Creates the search scope.
     *
     * @param Builder $builder
     * @param string $term
     * @return Builder
     */
    public function scopeSearch(Builder $builder, string $term): Builder
    {
        if ($builder->exactSearch($term)->count() > 0) {
            return $builder->exactSearch($term);
        }

        // TODO: this method has to be modified to work with any object
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~', '٬'];
        $term = str_replace($reservedSymbols, '', $term);
        $term = preg_replace("/[ ]+/", " ", $term);
        $words = static::getTermWords($term);

        foreach ($words as $word)
            if (!is_numeric($word) and strlen($word) > 1) {
                if (static::$EXACT_SEARCH_FIELD !== null)
                    $builder->where(static::$EXACT_SEARCH_FIELD, 'LIKE', "%$word%");
            }

        if ($builder->count() > 0) {
            return $builder;
        }

        $columns = implode(',', static::$SEARCHABLE_FIELDS);
        $match = "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)";
        $against = static::fullTextWildcards($term);
        $builder->orwhereRaw($match, [$against])->orderByRaw($match . " DESC", [$against]);
        if (static::$EXACT_SEARCH_ORDER_FIELD !== null)
            $builder->orderBy(static::$EXACT_SEARCH_ORDER_FIELD, 'DESC');
        return $builder;
    }

    protected static function fullTextWildcards($term): string
    {
        $words = static::getTermWords($term);
        foreach ($words as $key => $word) {
            $prefix = null;
            $postfix = null;
            if (strlen($word) >= 4) {
                $prefix = '*';
                $postfix = '*';
                if (strlen($word) >= 6)
                    $prefix = '+';
            } else
                $prefix = '-';
            $words[$key] = $prefix . $word . $postfix;
        }
        return implode(' ', $words);
    }

    /**
     * @param $term
     * @return array
     */
    protected static function getTermWords($term): array
    {
        return explode(' ', trim($term));
    }

}
