<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Models\Product;
use App\Models\SystemUser;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (get_system_user()?->is_super_user)
            return $next($request);

        $directory_id = $this->getDirectoryId($request);

        if ($directory_id != null) {
            try {
                SystemUser::where("user_id", auth($guard)->id())->whereHas('roles', function ($query) use ($directory_id) {
                    $query->whereHas('directories', function ($query) use ($directory_id) {
                        $query->where('id', $directory_id);
                    });
                })->firstOrFail();
                //TODO: Shouldn't fail for directories without role to show to all system users
            } catch (Exception $e) {
                abort(403);
            }
        }
        return $next($request);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getDirectoryId($request)
    {
        if ($request->has('directory_id'))
            return $request->get('directory_id');

        $urlParts = $request->segments();

        if ($this->hasIdInRequest($urlParts, 'directory'))
            return $urlParts[2];

        if ($this->hasIdInRequest($urlParts, 'product'))
            return Product::find($urlParts[2])->directory_id;

        if ($this->hasIdInRequest($urlParts, 'article'))
            return Article::find($urlParts[2])->directory_id;

        return null;
    }

    private function hasIdInRequest($segments, $category)
    {
        return (count($segments) >= 3) and ($segments[1] == $category) and is_numeric($segments[2]);
    }
}
