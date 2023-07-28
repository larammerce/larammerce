<?php

namespace App\Http\Controllers\Admin;

use App\Features\Excel\ExcelCacheConfig;
use App\Features\Excel\ModelExport;
use App\Features\Excel\ModelImport;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use App\Utils\Excel\Facades\Excel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ExcelController extends BaseController
{
    public function export(Request $request): BinaryFileResponse|RedirectResponse {
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", -1);
        $fields = json_decode($request->get('exporting_fields'), true);
        $relations = json_decode($request->get('exporting_relations'), true);
        if (count($fields ?? []) > 0 || count($relations ?? []) > 0) {
            $model_name = $request->get('model_name');
            ExcelCacheConfig::update($model_name, $fields, $relations);
            $file_name = get_model_entity_name($model_name) . '.xlsx';
            return Excel::download(
                new ModelExport(
                    model_name: $model_name,
                    fields: $fields,
                    model_relations: $relations,
                    query_data: $request->get("query_data") ?? [],
                    group_by: $request->get("group_by") ?? [],
                    extended_attributes: $request->get("extended_attributes") ?? [],
                    raw_query: $request->get("raw_query") ?? "",
                    raw_select: $request->get("raw_select") ?? ""
                ),
                $file_name
            );
        } else {
            return redirect()->back();
        }

    }

    public function viewImport($related_model): Application|Factory|View|RedirectResponse {
        if ($related_model::getImportableAttributes() != null) {
            return view('admin.pages.excel.import', compact('related_model'));
        } else {
            SystemMessageService::addErrorMessage('messages.excel.importable_attributes_not_set');
            return History::redirectBack();
        }

    }

    public function import(Request $request): RedirectResponse {
        $file = $request->file('file');
        $model_name = $request->get('model_name');
        $model_import = new ModelImport($file, $model_name);
        $model_import->import();
        $errors = $model_import->getValidationErrors();
        return redirect()->back()->with('import_errors', $errors);
    }

    public function getImportSample($model_name): BinaryFileResponse {
        $file_name = get_model_entity_name($model_name) . '-import-sample.xlsx';
        $fields = array_keys($model_name::getImportableAttributes());
        return Excel::download(new ModelExport($model_name, $fields, [], true), $file_name);
    }

    public function getModel(): ?string {
        return null;
    }
}
