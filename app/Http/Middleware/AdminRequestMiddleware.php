<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/26/16
 * Time: 8:31 PM
 */

namespace App\Http\Middleware;

use App\Utils\CMS\ActionLogService;
use App\Utils\CMS\Appliance\ApplianceService;
use App\Utils\CMS\Setting\Layout\LayoutModel;
use App\Utils\CMS\Setting\Layout\LayoutService;
use App\Utils\CMS\Setting\Pagination\PaginationModel;
use App\Utils\CMS\Setting\Pagination\PaginationService;
use App\Utils\CMS\Setting\Sort\SortModel;
use App\Utils\CMS\Setting\Sort\SortService;
use App\Utils\Reflection\Action;
use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use Closure;
use ReflectionException;

/**
 * Class AdminRequestMiddleware
 * @package App\Http\Middleware
 */
class AdminRequestMiddleware
{

    private static string $sortModelProperty = "sort_model";
    private static string $sortFieldProperty = "sort_field";
    private static string $sortMethodProperty = "sort_method";

    private static string $layoutModelProperty = "layout_model";
    private static string $layoutMethodProperty = "layout_method";

    private static string $paginationModelProperty = "pagination_model";
    private static string $paginationPageProperty = "pagination_page";
    private static string $paginationParentIdProperty = "pagination_parent_id";

    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $user = get_user($guard);
            $systemUser = $user->systemUser;
            $action = Action::withRequest($request);

            if (!$request->has("related_model"))
                $request->merge(["related_model" => app($action->getClassName())?->getModel()]);

            $this->setOrderAttributes($request);
            $this->setLayoutAttributes($request);
            $this->setPaginationAttributes($request);

            ApplianceService::init();

            $classAnnotation = $action->getClass()->getAnnotation("role");
            if ($classAnnotation->checkProperty("enabled", true)) {
                $methodAnnotation = $action->getMethod()->getAnnotation("role");
                foreach ($methodAnnotation->getPropertyNames() as $propertyName) {
                    if (isset($systemUser->{"is_" . $propertyName}) and
                        $systemUser->{"is_" . $propertyName}) {
                        ActionLogService::saveAction($action, $user);
                        return $next($request);
                    }
                }
                ActionLogService::saveAction($action, $user, false);
                return abort(403);
            }

            ActionLogService::saveAction($action, $user);
            return $next($request);
        } catch (AnnotationNotFoundException|ReflectionException|AnnotationSyntaxException|AnnotationBadScopeException|AnnotationBadKeyException $e) {
            return $next($request);
        }
    }

    private function setOrderAttributes($request)
    {
        if ($request->has(self::$sortModelProperty) and
            $request->has(self::$sortFieldProperty) and
            $request->has(self::$sortMethodProperty)) {

            $sortModel = new SortModel();
            $sortModel->setModelName($request->get(self::$sortModelProperty));
            $sortModel->setField($request->get(self::$sortFieldProperty));
            $sortModel->setMethod($request->get(self::$sortMethodProperty));

            SortService::setRecord($sortModel);
        }
    }

    private function setLayoutAttributes($request)
    {
        if ($request->has(self::$layoutMethodProperty) and
            $request->has(self::$layoutModelProperty)) {

            $layoutModel = new LayoutModel();
            $layoutModel->setModel($request->get(self::$layoutModelProperty));
            $layoutModel->setMethod($request->get(self::$layoutMethodProperty));

            LayoutService::setRecord($layoutModel);
        }
    }

    private function setPaginationAttributes($request)
    {
        if ($request->has(self::$paginationModelProperty) and
            $request->has(self::$paginationPageProperty)) {

            $paginationModel = new PaginationModel();
            $paginationModel->setModel($request->get(self::$paginationModelProperty));
            $paginationModel->setPage($request->get(self::$paginationPageProperty));

            if ($request->has(self::$paginationParentIdProperty))
                PaginationService::setRecord($paginationModel, $request->get(self::$paginationParentIdProperty));
            else
                PaginationService::setRecord($paginationModel);
        }
    }
}
