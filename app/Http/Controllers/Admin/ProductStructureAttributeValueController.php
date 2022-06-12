<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductStructureAttributeKey;
use App\Models\ProductStructureAttributeValue;
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
class ProductStructureAttributeValueController extends BaseController
{
    /**
     * @rules(p_structure_attr_key_id="exists:p_structure_attr_keys,id")
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        if (request()->has('p_structure_attr_key_id')) {
            $key = ProductStructureAttributeKey::find(request()->get('p_structure_attr_key_id'));
            parent::setPageAttribute($key->id);
            $attribute_values = $key->values()->with('key')
                ->paginate(ProductStructureAttributeValue::getPaginationCount());
        } else {
            parent::setPageAttribute();
            $attribute_values = ProductStructureAttributeValue::with('key')
                ->paginate(ProductStructureAttributeValue::getPaginationCount());
        }

        return view('admin.pages.p-structure-attr-value.index', compact('attribute_values'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:p_structure_attr_keys")
     */
    public function create(): Factory|View|Application
    {
        $key = ProductStructureAttributeKey::find(request()->get('id'));
        return view('admin.pages.p-structure-attr-value.create', compact('key'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required", p_structure_attr_key_id="required|exists:p_structure_attr_keys,id",
     *     image="image|max:2048")
     */
    public function store(Request $request): RedirectResponse
    {
        $attribute_value = ProductStructureAttributeValue::create($request->all());

        if ($request->hasFile('image'))
            $attribute_value->setImagePath();

        $p_structure_attr_key = ProductStructureAttributeKey::find($request->get('p_structure_attr_key_id'));
        return redirect()->route('admin.p-structure-attr-key.edit', compact('p_structure_attr_key'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(ProductStructureAttributeValue $p_structure_attr_value): Factory|View|Application
    {
        $p_structure_attr_value->load('key');
        return view('admin.pages.p-structure-attr-value.edit')->with(['attribute_value' => $p_structure_attr_value]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required", image="image|max:2048")
     */
    public function update(Request $request, ProductStructureAttributeValue $p_structure_attr_value): RedirectResponse
    {
        $p_structure_attr_value->update($request->all());
        if ($request->hasFile('image'))
            $p_structure_attr_value->setImagePath();
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(ProductStructureAttributeValue $p_structure_attr_value): RedirectResponse
    {
        $p_structure_attr_key = $p_structure_attr_value->key;
        $p_structure_attr_value->delete();
        return redirect()->route('admin.p-structure-attr-key.edit', compact('p_structure_attr_key'));
    }

    /**
     * @role(super_user, seo_master, cms_manager)
     */
    public function removeImage(ProductStructureAttributeValue $p_structure_attr_value): RedirectResponse
    {
        $p_structure_attr_value->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return ProductStructureAttributeValue::class;
    }
}
