<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/26/16
 * Time: 8:31 PM
 */

namespace App\Http\Middleware;

use App\Helpers\RequestHelper;
use App\Libraries\Reflection\Action;
use Closure;

/**
 * Class JsonMiddleware
 * @package App\Http\Middleware\HaMiddleWares
 */
class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \App\Libraries\Reflection\AnnotationBadKeyException
     * @throws \App\Libraries\Reflection\AnnotationBadScopeException
     * @throws \App\Libraries\Reflection\AnnotationSyntaxException
     * @throws \ReflectionException
     */
    public function handle($request, Closure $next)
    {
        if (!RequestHelper::isRequestAjax($request)) {
            return response()->json(Action::withRequest($request));
        }
        return $next($request);
    }
}