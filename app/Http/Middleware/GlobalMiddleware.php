<?php

namespace App\Http\Middleware;

use App\Utils\Common\History;
use Closure;
use Illuminate\Http\Request;

class GlobalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return History::visit($request, $next);
    }
}
