<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 12/13/18
 * Time: 2:14 PM
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FullTextSearch
{

    /**
     * Creates the search scope.
     *
     * @param Builder $builder
     * @param string $term
     * @param int $exactness
     * @return Builder
     */
    public function scopeSearch(Builder $builder, string $term, int $exactness = 0): Builder
    {
        $parent_exact_builder = $builder->clone();
        if ($exactness == 3 or $parent_exact_builder->exactSearch($term)->count() > 0) {
            return $parent_exact_builder->exactSearch($term);
        }

        foreach(static::$IMPORTANT_SEARCH_FIELDS as $important_field){
            $exact_builder = $builder->clone();
            $exact_builder = $exact_builder->where($important_field, 'like', "$term%");
            $exact_search_result_count = $exact_builder->count();
            if ($exactness == 2 or $exact_search_result_count > 0) {
                if ($exact_search_result_count < 20) {
                    $exact_builder = $exact_builder->orWhere($important_field, 'like', "%$term%");
                }
                return $exact_builder;
            }
        }

        $non_exact_builder = $builder->clone();
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~', '٬'];
        $term = str_replace($reservedSymbols, '', $term);
        $term = preg_replace("/ +/", " ", $term);
        $words = static::getTermWords($term);
        $important_field = static::$IMPORTANT_SEARCH_FIELDS[0] ?? "id";
        foreach ($words as $word)
            if (!is_numeric($word) and strlen($word) > 1) {
                $non_exact_builder->where($important_field, 'LIKE', "%$word%");
            }
        if ($exactness == 1 or $non_exact_builder->count() > 0) {
            return $non_exact_builder;
        }

        $columns = implode(',', static::$SEARCHABLE_FIELDS);
        $match = "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)";
        $against = static::fullTextWildcards($term);
        $builder->whereRaw($match, [$against])->orderByRaw($match . " DESC", [$against]);
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
