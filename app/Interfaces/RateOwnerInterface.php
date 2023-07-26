<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/27/2017 AD
 * Time: 18:37
 */

namespace App\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property float average_rating
 * @property int rating_number
 *
 * Interface Ratable
 * @package App\Models\Interfaces
 */
interface RateOwnerInterface
{
    function rates(): MorphMany;

    function scopePopular(Builder $query): Builder;

    /**
     * @return string
     */
    function getTitle(): string;

    function submitRating(int $value, string $comment): bool;

    function deleteRating(): bool;
}
