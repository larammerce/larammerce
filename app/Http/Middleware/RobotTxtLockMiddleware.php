<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/13/19
 * Time: 11:36 AM
 */

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class RobotTxtLockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        //check lock for admin instance of robot txt service.
        return $next($request);
    }
}