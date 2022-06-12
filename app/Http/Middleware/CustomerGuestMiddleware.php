<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/17/17
 * Time: 4:59 PM
 */

namespace App\Http\Middleware;

use Closure;

class CustomerGuestMiddleware
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (!auth($guard)->guest()) {
            return redirect()->route('customer.profile.index');
        }

        return $next($request);
    }
}
