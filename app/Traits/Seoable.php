<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 7/17/2018 AD
 * Time: 08:46
 */

namespace App\Traits;


use App\Helpers\EloquentModelHelper;
use App\Models\Review;

/**
 * @property Review review
 *
 * Trait Seoable
 * @package App\Models\Traits
 */
trait Seoable
{
    /*
     *  Relation Methods
     */
    public function review()
    {
        return $this->morphOne(Review::class, 'reviewable');
    }

    public function createReview()
    {
        $this->review()->create([]);
    }

    public function updateReview()
    {
        $review = $this->review;
        if ($review) {
            $review->increaseEditCount();
        } else {
            $this->createReview();
        }
    }

    /*
     *  Helper Methods
     */
    public function getAdminEditUrl()
    {
        $modelName = EloquentModelHelper::className(get_class($this), true);
        return route("admin.{$modelName}.edit", $this);
    }

    public function getType()
    {
        $modelName = EloquentModelHelper::className(get_class($this), true);
        return trans("general.reviewable.{$modelName}");
    }

}
