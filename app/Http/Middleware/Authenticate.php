<?php

namespace App\Http\Middleware;

use App\Helpers\RequestHelper;
use Closure;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth($guard)->guest()) {
            if (RequestHelper::isRequestAjax($request)) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest('login');
        }

        return $next($request);
    }
}
