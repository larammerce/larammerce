<?php

namespace App\Http\Controllers\Admin;

/**
 * @role(enabled=true)
 */
class LiveReportsController extends BaseController
{
    /**
     * @role(super_user)
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("admin.pages.live-reports.index");
    }

    public function getModel(): ?string
    {
        return null;
    }
}
