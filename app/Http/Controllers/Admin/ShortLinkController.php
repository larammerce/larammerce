<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShortLink;
use App\Models\ShortLinkStatistic;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ShortLinkController extends BaseController
{
    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function index(): Factory|View|Application
    {
        $short_domain = config('app.shortened_host');
        parent::setPageAttribute();
        $short_links = ShortLink::paginate(ShortLink::getPaginationCount());
        return view('admin.pages.short-link.index', compact('short_links', 'short_domain'));
    }

    public function create(): Factory|View|Application
    {
        return view('admin.pages.short-link.create');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(link="required|url", shortened_link="required|unique:short_links")
     */
    public function store(Request $request): RedirectResponse
    {
        $short_link = ShortLink::create($request->all());
        $json_data = [
            "yearly" => [],
            "monthly" => [],
            "daily" => [],
        ];
        $short_link_stats = new ShortLinkStatistic();
        $short_link_stats->short_link_id = $short_link->id;
        $short_link_stats->views_count = 0;
        $short_link_stats->json_data = json_encode($json_data);
        $short_link_stats->save();
        return redirect()->route('admin.short-link.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function edit(ShortLink $short_link): Factory|View|Application
    {
        return view('admin.pages.short-link.edit', ["short_link" => $short_link]);
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(link="required|url",
     *        shortened_link="required|unique:short_links,shortened_link,".request()->short_link?->id)
     */
    public function update(Request $request, ShortLink $short_link): RedirectResponse
    {
        $short_link->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function destroy(ShortLink $short_link): RedirectResponse
    {
        $short_link->delete();
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function showStats(ShortLink $short_link): Factory|View|Application
    {
        $short_link_stats = $short_link->statistics->first();
        $stats_data_raw = $short_link_stats->json_data;
        $total_count = $short_link_stats->views_count;
        return view('admin.pages.short-link.statistics',
            ["stats_data" => $stats_data_raw, "total_count" => $total_count]);
    }


    public function getModel(): ?string
    {
        return ShortLink::class;
    }
}
