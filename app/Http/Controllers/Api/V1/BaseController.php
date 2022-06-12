<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

/**
 * Class BaseController
 * @package App\Http\Controllers\Api\V1
 */
abstract class BaseController extends Controller
{
    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
