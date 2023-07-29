<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\EnvFile\EnvFileService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EnvFileController extends BaseController {
    private EnvFileService $env_file_service;

    public function __construct(EnvFileService $env_file_service) {
        $this->env_file_service = $env_file_service;
        parent::__construct();
    }

    public function edit(Request $request): Factory|View|Application {
        $env_vars = $this->env_file_service->getCurrentEnvVars();
        $missing_vars = $this->env_file_service->getMissingEnvVars();
        $deprecated_keys = $this->env_file_service->getDeprecatedEnvVars()->keys()->all();

        return view('admin.pages.env-file.edit', compact('env_vars', 'missing_vars', 'deprecated_keys'));
    }

    public function update(Request $request){
        $this->env_file_service->storeEnvVars(collect($request->get('env_rows')));

        return redirect()->route('admin.setting.env-file.edit');
    }

    public function getModel(): ?string {
        return null;
    }
}