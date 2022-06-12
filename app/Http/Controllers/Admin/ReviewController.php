<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ReviewController extends BaseController
{
    /**
     * @role(super_user, seo_master)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();

        $reviews = Review::with('reviewable')->paginate(Review::getPaginationCount());
        return view('admin.pages.review.index', compact('reviews'));
    }

    public function setAsChecked(Review $review): RedirectResponse
    {
        $review->setAsChecked();
        return redirect()->route('admin.review.index');
    }


    public function getModel(): ?string
    {
        return Review::class;
    }
}
