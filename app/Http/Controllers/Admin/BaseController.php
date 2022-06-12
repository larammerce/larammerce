<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utils\CMS\Setting\Pagination\PaginationService;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 */
abstract class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setPageAttribute($parentId = null, ?string $model = null)
    {
        if ($this->getModel() !== null)
            PaginationService::initiate($this->getModel(), $parentId);
        else if ($model !== null)
            PaginationService::initiate($model, $parentId);
    }

    public abstract function getModel(): ?string;

    /**
     * @rules(query="required")
     */
    public function search(Request $request)
    {
        $result = [];
        eval("\$result = {$this->getModel()}::search(\$request->get('query'))->get();");
        return $result;
    }
}
