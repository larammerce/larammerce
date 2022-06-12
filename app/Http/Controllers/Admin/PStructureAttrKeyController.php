<?php

namespace App\Http\Controllers\Admin;

use App\Models\PStructureAttrKey;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class PStructureAttrKeyController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $p_structure_attr_keys = PStructureAttrKey::with('productStructures', 'values')
            ->paginate(PStructureAttrKey::getPaginationCount());
        return view('admin.pages.p-structure-attr-key.index', compact('p_structure_attr_keys'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.p-structure-attr-key.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required|unique:p_structure_attr_keys")
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $p_structure_attr_key = PStructureAttrKey::create($request->all());
        if (RequestService::isRequestAjax($request)) {
            return response()->json(MessageFactory::create(
                ['messages.p_structure_attr_key.key_added'], 200, compact('p_structure_attr_key')
            ), 200);
        }
        return redirect()->route('admin.p-structure-attr-key.edit', $p_structure_attr_key);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(PStructureAttrKey $p_structure_attr_key): Factory|View|Application
    {
        $p_structure_attr_key->load('productStructures', 'values');
        return view('admin.pages.p-structure-attr-key.edit')->with(['p_structure_attr_key' => $p_structure_attr_key]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required", has_value="boolean", is_sortable="boolean")
     */
    public function update(Request $request, PStructureAttrKey $p_structure_attr_key): RedirectResponse
    {
        $p_structure_attr_key->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(PStructureAttrKey $p_structure_attr_key): RedirectResponse
    {
        $p_structure_attr_key->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(query="required")
     */
    public function query(Request $request)
    {
        $collection = PStructureAttrKey::where('title', 'like', '%' . $request->input('query') . '%')->get();
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                [], 200, compact('collection')
            ), 200);
        }
        return response(json_encode($collection));
    }


    public function getModel(): ?string
    {
        return PStructureAttrKey::class;
    }
}
