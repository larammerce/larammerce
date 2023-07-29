<?php

namespace App\Http\Middleware;

use App\Helpers\HistoryHelper;
use Closure;
use Illuminate\Http\Request;

class GlobalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return HistoryHelper::visit($request, $next);
    }
}
