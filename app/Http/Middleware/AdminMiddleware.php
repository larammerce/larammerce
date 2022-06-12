<?php

namespace App\Http\Middleware;

use App\Utils\Common\RequestService;
use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth($guard)->guest()) {
            if (RequestService::isRequestAjax($request)) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest('login');
        } else if (!get_user($guard)->is_system_user) {
            return abort(403);
        }

        return $next($request);
    }
}
