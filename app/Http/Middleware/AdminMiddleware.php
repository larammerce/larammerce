<?php

namespace App\Http\Middleware;

use App\Helpers\RequestHelper;
use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth($guard)->guest()) {
            if (RequestHelper::isRequestAjax($request)) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest('login');
        } else if (!get_user($guard)->is_system_user) {
            return abort(403);
        }

        return $next($request);
    }
}
