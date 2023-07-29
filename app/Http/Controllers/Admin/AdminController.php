<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Libraries\Reflection\ReflectiveNamespace;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @role(enabled=true)
 */
class AdminController extends BaseController
{

    public function nullMethod(): RedirectResponse
    {
        return HistoryHelper::redirectBack();
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('admin.directory.index');
    }

    public function settingAppliances(): Factory|View|Application
    {
        return view('admin.pages.setting.appliances');
    }

    public function shopAppliances(): Factory|View|Application
    {
        return view('admin.pages.shop.appliances');
    }

    public function analyticAppliances(): Factory|View|Application
    {
        return view('admin.pages.analytic.appliances');
    }

    public function classicSearch(Request $request): \Response|View|Factory|Response|RedirectResponse|Application|ResponseFactory
    {
        $related_model = $request->get('related_model');
        parent::setPageAttribute('scope_classic_search', $related_model);
        if ($this->verifySearchedObject($related_model)) {
            $searchable_fields = $related_model::getSearchableFields();
            $searched_terms = $request->only($searchable_fields);
            $objects = $related_model::classicSearch($searched_terms)->paginate($related_model::getPaginationCount());

            $entity_name = get_model_entity_name($related_model);

            $snake_cased_string = Str::snake($entity_name);
            $dashed_string = str_to_dashed($entity_name);
            $variable_string = Str::plural($snake_cased_string);

            try {
                return view('admin.pages.' . $dashed_string . '.index', [
                    $variable_string => $objects,
                    'related_model' => $related_model,
                    'scope' => 'scope_classic_search'
                ]);
            } catch (InvalidArgumentException $e) {
                return HistoryHelper::redirectBack();
            }

        } else {
            return response('object not valid');
        }
    }


    private function verifySearchedObject($object): bool
    {
        $existing_models = (new ReflectiveNamespace("\\App\\Models"))->getClassNames();
        return in_array($object, $existing_models);
    }

    /**
     * @role(super_user)
     */
    public function loginAs(User $user): RedirectResponse
    {
        auth("web")->logout();
        auth('web_eloquent')->logout();
        auth('web')->login($user);
        return redirect()->to('/');
    }

    public function getModel(): ?string
    {
        return null;
    }
}
