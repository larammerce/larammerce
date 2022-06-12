<?php

namespace App\Http\Middleware;

use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Closure;

class CustomerMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth($guard)->guest()) {
            if (RequestService::isRequestAjax($request)) {
                return MessageFactory::jsonResponse(["auth.not_logged_in"], 401);
            }
            return redirect()->guest(route('customer-auth.show-auth',
                config("auth.default_type.customer")));
        } else if (!auth($guard)->user()->is_customer_user) {
            if (RequestService::isRequestAjax($request)) {
                return MessageFactory::jsonResponse(["auth.forbidden"], 403);
            }
            return abort(403);
        }
        return $next($request);
    }
}
