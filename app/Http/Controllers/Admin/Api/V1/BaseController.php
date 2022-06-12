<?php

namespace App\Http\Controllers\Admin\Api\V1;

use App\Http\Controllers\Admin\BaseController as Controller;

/**
 * @package App\Http\Controllers\Api\V1
 */
abstract class BaseController extends Controller
{
    /**
     */
    public function __construct()
    {
        parent::__construct();
    }
}
