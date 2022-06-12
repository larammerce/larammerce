<?php

namespace App\Http\Middleware;

use App\Utils\CMS\UserService;
use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth($guard)->check()) {
            return redirect(UserService::getHome(get_user($guard)));
        }

        return $next($request);
    }
}
