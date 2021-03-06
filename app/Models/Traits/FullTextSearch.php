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
        // TODO: this method has to be modified to work with any object
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~', '٬'];
        $term = str_replace($reservedSymbols, '', $term);
        $term = preg_replace("/[ ]+/", " ", $term);
        $columns = implode(',', static::$SEARCHABLE_FIELDS);
        $against = static::fullTextWildcards($term);
        $match = "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)";
        $words = static::getTermWords($term);
        foreach ($words as $word)
            if (!is_numeric($word) and strlen($word) > 2) {
                if (static::$EXACT_SEARCH_FIELD !== null)
                    $builder->where(static::$EXACT_SEARCH_FIELD, 'LIKE', "%$word%");
                if (static::$EXACT_SEARCH_FIELD !== null)
                    $builder->orderBy(static::$EXACT_SEARCH_ORDER_FIELD, 'DESC');
            }
        $builder->orwhereRaw($match, [$against])->orderByRaw($match . " DESC", [$against]);
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
