<?php

namespace App\Http\Controllers\Admin;

use App\Models\PAttr;
use App\Models\PStructure;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class PStructureController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|\Illuminate\Contracts\View\View|Application
    {
        parent::setPageAttribute();
        $p_structures = PStructure::with('attributeKeys')
            ->paginate(PStructure::getPaginationCount());
        return view('admin.pages.p-structure.index', compact('p_structures'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|\Illuminate\Contracts\View\View|Application
    {
        return view('admin.pages.p-structure.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required|unique:p_structures")
     */
    public function store(Request $request): RedirectResponse
    {
        $p_structure = PStructure::create($request->all());
        return redirect()->route('admin.p-structure.edit', $p_structure);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(PStructure $p_structure): Factory|\Illuminate\Contracts\View\View|Application
    {
        $p_structure->load('attributeKeys');
        return view('admin.pages.p-structure.edit')->with(compact("p_structure"));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required|unique:p_structures,title," . request()->get('id'))
     */
    public function update(Request $request, PStructure $p_structure): RedirectResponse
    {
        $p_structure->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(PStructure $p_structure): RedirectResponse
    {
        $p_structure->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(attributeKeys="array", attributeKeys.*="exists:p_structure_attribute_keys,id")
     */
    public function attachAttributeKeys(Request $request, PStructure $p_structure): RedirectResponse
    {
        $p_structure->attributeKeys()->detach();
        $p_structure->attributeKeys()->attach($request->get('attributeKeys'));
        return redirect()->route('admin.pages.p-structure.index');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:p_structure_attr_keys,id")
     */
    public function attachAttributeKey(Request $request, PStructure $p_structure): JsonResponse|RedirectResponse
    {
        $p_structure->attributeKeys()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.p_structure.attribute_key_attached'], 200,
                compact('p_structure')
            ), 200);
        }
        return redirect()->route('admin.p-structure.index');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:p_structure_attr_keys,id")
     */
    public function detachAttributeKey(Request $request, PStructure $p_structure): JsonResponse|RedirectResponse
    {
        $p_structure->attributeKeys()->detach($request->get('id'));
        PAttr::clean($p_structure->products, $request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.p_structure.attribute_key_detached'], 200,
                compact('p_structure')
            ), 200);
        }
        return redirect()->route('admin.p-structure.index');
    }


    public function getModel(): ?string
    {
        return PStructure::class;
    }
}
