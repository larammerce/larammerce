<?php

namespace App\Http\Middleware;

use App\Helpers\AdminRequestHelper;
use Closure;
use Illuminate\Http\Request;

class Translate
{
    public function handle(Request $request, Closure $next)
    {
        if (!AdminRequestHelper::IsInAdminArea($request)) {
            if ($request->has("locale_fallback")) {
                return redirect()->to(config('translation.fallback_locale') . $request->getRequestUri());
            }
            if ($request->has("locale")) {
                app()->setLocale($request->get("locale"));
            }
        }
        return $next($request);
    }
}
