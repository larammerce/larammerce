<?php
/**
 */

namespace App\Http\Controllers\Admin\Api\V1;

use App\Helpers\ResponseHelper;
use App\Models\Directory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin\Api\V1
 * @role(enabled=true)
 */
class DirectoryController extends BaseController
{
    /**
     * @role(super_user, acc_manager, stock_manager, cms_manager)
     * @rules(query="required")
     */
    public function query(Request $request): JsonResponse
    {
        $collection = Directory::search($request->get('query'))->get();

        return response()->json(ResponseHelper::create(
            [], 200, compact('collection')
        ), 200);
    }

    public function getModel(): ?string
    {
        return Directory::class;
    }
}
