<?php

namespace App\Http\Controllers\Admin;

use App\Utils\Common\History;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ModelTranslationController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     * @rules(lang_id="required|string", related_model="required|string", related_object_id="required|integer|min:1")
     */
    //TODO: add rule related_object_id=required|exits:table_name($request->get(related_model)),id
    public function edit(Request $request): View|RedirectResponse
    {
        try {
            $lang_id = $request->get("lang_id");
            $related_model = $request->get('related_model');
            $related_object_id = $request->get('related_object_id');
            $translatable_object = $related_model::findOrFail($related_object_id);
            $translatable_object->setDefaultLocale($lang_id);
            $translatable_fields = $related_model::getTranslatableFields(with_input_type: true);
            $translation_edit_form = $related_model::getTranslationEditForm();

            $entity_name = get_model_entity_name($related_model);
            $entity_name_dashed = str_to_dashed($entity_name);

            if ($lang_id == config("translation.fallback_locale"))
                return redirect()->route("admin.$entity_name_dashed.edit", $translatable_object);

            return view($translation_edit_form, [
                'translatable_object' => $translatable_object,
                'translatable_fields' => $translatable_fields,
                $entity_name => $translatable_object,
                'related_model' => $related_model,
                'lang_id' => $lang_id,
                'entity_name' => $entity_name
            ]);
        } catch (Exception $e) {
            return History::redirectBack();
        }
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(lang_id="required|string", related_model="required|string")
     */
    //TODO: add dynamic rule lang_id=required|exits in config(translation.locales)
    public function update(Request $request): RedirectResponse
    {
        $lang_id = $request->get("lang_id");
        $related_model = $request->get('related_model');
        $translatable_object = $related_model::find($request->get('id'));
        $translatable_fields = $related_model::getTranslatableFields(with_input_type: true);
        foreach ($translatable_fields as $translatable_field => $field_type) {
            if ($field_type == "json") {
                $translatable_object->translateOrNew($lang_id)->$translatable_field = json_encode($request->get($translatable_field));
            } else {
                $translatable_object->translateOrNew($lang_id)->$translatable_field = $request->get($translatable_field);
            }
        }
        $translatable_object->save();
        $defaultResponse = redirect()->route("admin." . str_to_dashed(get_model_entity_name($related_model)) . ".edit",
            $translatable_object);
        return History::redirectBack($defaultResponse);
    }

    public function getModel(): ?string
    {
        return null;
    }
}
