<?php

namespace App\Http\Controllers\Admin;

use App\Models\WebForm;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class WebFormController extends BaseController
{
    public function getModel(): ?string
    {
        return WebForm::class;
    }
}
