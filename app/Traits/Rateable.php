<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/18/18
 * Time: 1:17 PM
 */

namespace App\Traits;


use App\Models\Rate;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

/**
 * @property Rate[] rates
 * @property Rate[] approved_rates
 *
 * Trait Rateable
 * @package App\Models\Traits
 */
trait Rateable
{
    /**
     * @return MorphMany
     */
    public function rates(): MorphMany
    {
        return $this->morphMany(Rate::class, 'object');
    }

    public function approvedRates()
    {
        return $this->rates()->where('is_accepted', true);
    }

    public function getApprovedRatesAttribute()
    {
        return $this->approvedRates()->get();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('average_rating', 'DESC')->orderBy('rates_count', 'DESC');
    }

    /**
     * @param integer $value
     * @param string $comment
     * @return boolean
     */
    private function addRating(int $value, string $comment): bool
    {
        try {
            $this->rates()->create([
                'value' => $value,
                'comment' => $comment,
                'customer_user_id' => get_customer_user()->id,
                'is_accepted' => false
            ]);

            $this->average_rating = (($this->average_rating * $this->rates_count) + $value) / ($this->rates_count + 1);
            $this->rates_count++;
            $this->save();

            return true;
        } catch (Exception $e) {
            Log::error("RATE.ERROR -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * @param integer $value
     * @param string $comment
     * @return boolean
     */
    public function submitRating(?int $value, ?string $comment): bool
    {
        $rate = $this->rates()->authCustomer()->first();

        $value = $value ?? 0;
        $comment = $comment ?? "";

        if ($rate == null) {
            return $this->addRating($value, $comment);
        }

        try {
            $diff = $value - $rate->value;
            $this->average_rating = (($this->average_rating * $this->rates_count) + $diff) / $this->rates_count;
            $this->save();

            $rate->update([
                'value' => $value,
                'comment' => $comment,
                'is_accepted' => false
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("RATE.ERROR -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * @return boolean
     */
    public function deleteRating(): bool
    {
        $rate = $this->rates()->authCustomer()->first();

        if ($rate == null) {
            return false;
        }

        try {
            $this->average_rating = (($this->average_rating * $this->rates_count) - $rate->value) / ($this->rates_count - 1);
            $this->rates_count--;
            $this->save();

            $rate->delete();

            return true;
        } catch (Exception $e) {
            Log::error("RATE.ERROR -> " . $e->getMessage());
            return false;
        }
    }
}
